<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\ArchiveProductInputDto;
use App\Dto\MassSaveProductInputDto;
use App\Dto\ResponseDto;
use App\Entity\Product;
use App\Entity\ProductPrice;
use App\Entity\ProductStock;
use App\Entity\ProductUserData;
use App\Enum\ProductFlag;
use App\Repository\ProductRepository;
use App\Service\DateService;
use App\Service\ShopService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @implements ProcessorInterface<Product, Product>
 */
final class SaveProductProcessor implements ProcessorInterface
{
    private array $productData = [];
    private array $flags = [];
    private bool $isNew = true;

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private DateService $dateService,
        private Security $security,
        private ProductRepository $productRepository,
        private RequestStack $requestStack,
        private ShopService $shopService,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    /**
     * @throws \RequestParseBodyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!($operation instanceof Patch || $operation instanceof Post)) {
            return null;
        }

        if ($data instanceof MassSaveProductInputDto) {
            $errorMessage = [];
            $savedProduct = [];
            $errorsCount = 0;

            $denormalizationContext = $operation->getDenormalizationContext();

            foreach ($data->products as $index => $requestData) {
                $this->isNew = false;

                try {
                    if (!empty($requestData['id'])) {
                        throw new InvalidArgumentException(\sprintf('Поле id пустое в products[%s].', $index));
                    }

                    if (!empty($requestData['shop_product_id'])) {
                        throw new InvalidArgumentException(
                            \sprintf('Поле shop_product_id пустое в products[%s].', $index)
                        );
                    }

                    $foundProduct = $this->productRepository->find($requestData['id']);

                    if (!$foundProduct) {
                        throw new EntityNotFoundException(
                            \sprintf(
                                'Не найден product с id = "%s" в products[%s].',
                                $requestData['id'],
                                $index
                            )
                        );
                    }

                    if ($foundProduct->getShopProductId() !== $requestData['shop_product_id']) {
                        throw new InvalidArgumentException(
                            \sprintf(
                                'Поле shop_product_id = %s не совпадает с полем сущности Product в products[%s].',
                                $requestData['shop_product_id'],
                                $index
                            )
                        );
                    }

                    $validationContext = $denormalizeContext = [];
                    $denormalizeContext[AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE] = true;
                    $denormalizeContext[AbstractNormalizer::OBJECT_TO_POPULATE] = $foundProduct;
                    $denormalizeContext['groups'] = $validationContext['groups'] =
                        [Product::GROUP_UPDATE, ProductUserData::GROUP_UPDATE,
                        ];

                    $denormalizationContext['groups'] = $denormalizeContext['groups'];
                    $operation = $operation
                        ->withDenormalizationContext($denormalizationContext)
                        ->withValidationContext($validationContext);

                    $denormalizeContext['operation'] = $operation;

                    $product = $this->serializer->denormalize(
                        $requestData,
                        Product::class,
                        'json',
                        $denormalizeContext
                    );

                    $this->productData = $requestData;
                    $productToProcess = $this->saveAction($product, $operation);

                    $savedProduct[] = $this->persistProcessor->process(
                        $productToProcess,
                        $operation,
                        $uriVariables,
                        $context
                    );
                } catch (\Throwable $e) {
                    ++$errorsCount;
                    $errorMessage[] = $e->getMessage();
                }
            }

            $result = [
                'products_count' => \count($data->products),
                'errors_count' => $errorsCount,
                'saved_count' => \count($savedProduct),
                'products' => $savedProduct,
            ];

            $result[ResponseDto::MESSAGE] = $errorMessage ? implode('. ', array_unique($errorMessage)) : 'Products are saved';
            $result[ResponseDto::SUCCESS] = !$errorsCount;

            return $result;
        }

        if ($data instanceof ArchiveProductInputDto) {
            $request = $this->requestStack->getCurrentRequest();

            $product = $this->archiveAction(
                $request->getPayload()->get('shop_product_id'),
                $request->getPayload()->get('value'),
            );

            if (!$product) {
                return [
                    'message' => 'Product is not exists',
                    'require_to_create' => true,
                ];
            }

            return [
                'product' => $this->persistProcessor->process($product, $operation, $uriVariables, $context),
            ];
        }

        if ($data instanceof Product) {
            $this->isNew = $operation->getName() === Product::ACTION_CREATE;
            $request = $this->requestStack->getCurrentRequest();
            $requestData = $request ? $request->getPayload()->all() : [];

            $this->productData = $requestData;
            $product = $this->saveAction($data, $operation);

            return [
                'product' => $this->persistProcessor->process($product, $operation, $uriVariables, $context),
            ];
        }

        return null;
    }

    /**
     * @throws \RequestParseBodyException
     */
    private function saveAction(Product $product, Operation $operation): Product
    {
        $this->flags = $this->productData['flags'] ?? [];
        $this->checkShopProductCode();

        /*
         * Если находит товар с не пустым shop_product_code у wildberries, значит уже заменён shop_product_id.
         * Сохранение не производим, передаваемые ID неверны.
         */
        $toChangeId = isset($this->flags[ProductFlag::ChangeId->value]) && $this->shopService->isWildberriesShopType();

        if ($toChangeId && !$product->getId()) {
            if (empty($this->productData['shop_product_id'])) {
                throw new \RequestParseBodyException('Поле shop_product_id не заполнено');
            }

            $foundProduct = $this->productRepository->findByShopProductId($this->productData['shop_product_id']);

            if ($foundProduct) {
                return $foundProduct;
            }
        }

        $this->addPrices($product);
        $this->addStocks($product);
        $this->addProductUserData($product);
        $product->setUserCreated($this->security->getUser());

        $this->validator->validate($product, $operation->getValidationContext());

        return $product;
    }

    private function archiveAction(string $shopProductId, bool $value): ?Product
    {
        $foundProduct = $this->productRepository->findByShopProductId($shopProductId);

        if ($foundProduct) {
            $foundProduct->getProductUserData()->setIsArchive($value);

            return $foundProduct;
        }

        return null;
    }

    /**
     * @throws \RequestParseBodyException
     */
    private function checkShopProductCode(): void
    {
        if (!$this->shopService->isWildberriesShopType()) {
            return;
        }

        if (!empty($this->productData['shop_product_code'])) {
            return;
        }

        throw new \RequestParseBodyException('Поле shop_product_code не заполнено');
    }

    private function addPrices(Product $product): void
    {
        $prices = $this->productData['prices'] ?? [];

        $toSaveProductPrices = ($this->flags[ProductFlag::SavePrices->value] ?? false) && !empty($prices);

        if (!$toSaveProductPrices) {
            return;
        }

        if (!$this->isNew) {
            $minPrice = $product->getMinPrice();

            if ($minPrice && end($prices)['price'] >= $minPrice) {
                return;
            }
        }

        foreach ($prices as $priceDate) {
            $date = new \DateTime($priceDate['date']);

            $newPrice = new ProductPrice();
            $newPrice->setUserCreated($this->security->getUser());
            $newPrice->setDateCreated($date);
            $newPrice->setDateCreatedString($date->format('Y-m-d H:i:s P'));
            $newPrice->setPrice($priceDate['price']);

            $product->addPrice($newPrice);
        }
    }

    private function addStocks(Product $product): void
    {
        $stocks = $this->productData['stocks'] ?? [];

        $toSaveProductStocks = ($this->flags[ProductFlag::SaveStocks->value] ?? false)
            && !empty($stocks);

        if (!$toSaveProductStocks) {
            return;
        }

        if (!$this->isNew) {
            $lastStock = end($stocks);
            $productLastStock = $product->getLastStock();

            $isLastStockEqualsQty = $lastStock && $productLastStock
                && $productLastStock->getDateCreated()->format('d.m.Y') === $this->dateService->getDateTime($lastStock['date'])
                    ->format('d.m.Y')
                && $productLastStock->getQty() === $lastStock['qty'];

            if ($isLastStockEqualsQty) {
                return;
            }
        }

        foreach ($stocks as $stock) {
            $date = new \DateTime($stock['date']);

            $newStock = new ProductStock();
            $newStock->setUserCreated($this->security->getUser());
            $newStock->setDateCreated($date);
            $newStock->setDateCreatedString($date->format('Y-m-d H:i:s P'));
            $newStock->setQty($stock['qty']);
            $newStock->setlog($stock['log']);

            $product->addStock($newStock);
        }
    }

    private function addProductUserData(Product $product): void
    {
        $pud = $product->getProductUserData();
        $pud->setUserCreated($this->security->getUser());
        $pud->setProduct($product);
    }
}
