<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Product;
use App\Entity\ProductPrice;
use App\Entity\ProductStock;
use App\Enum\ProductFlag;
use App\Repository\ProductRepository;
use App\Service\DateService;
use App\Service\ShopService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @implements ProcessorInterface<Product, Product>
 */
class PatchProductProcessor implements ProcessorInterface
{
    private array $parsedPayload = [];
    private array $flags = [];

    public function __construct(
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
     * @throws ExceptionInterface
     * @throws \RequestParseBodyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Product) {
            return null;
        }

        if (!$operation instanceof Patch) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $this->parsedPayload = $request->getPayload()->all();
            $this->flags = $this->parsedPayload['flags'] ?? [];
        }

        $this->checkShopProductCode();

        $toChangeId = isset($flags[ProductFlag::ChangeId->value]) && $this->shopService->isWildberriesShopType();

        if ($toChangeId && !$data->getId()) {
            if (empty($this->parsedPayload['shop_product_id'])) {
                throw new \RequestParseBodyException('Поле shop_product_id не заполнено');
            }

            $foundProduct = $this->productRepository->findBy(
                [
                    'shop_product_id' => $this->parsedPayload['shop_product_id'],
                    'shop_id' => $this->shopService->getShopId(),
                ]
            );

            if ($foundProduct) {
                $data = $foundProduct;
            }
        }

        $this->addPrices($data);
        $this->addStocks($data);

        return [
            'product' => $this->persistProcessor->process($data, $operation, $uriVariables, $context),
        ];
    }

    /**
     * @throws \RequestParseBodyException
     */
    private function checkShopProductCode(): void
    {
        if (!$this->shopService->isWildberriesShopType()) {
            return;
        }

        if (!empty($this->parsedPayload['shop_product_code'])) {
            return;
        }

        throw new \RequestParseBodyException('Поле shop_product_code не заполнено');
    }

    private function addPrices(Product $product): void
    {
        $prices = $this->parsedPayload['prices'] ?? [];

        $toSaveProductPrices = ($this->flags[ProductFlag::SavePrices->value] ?? false) && !empty($prices);

        if (!$toSaveProductPrices) {
            return;
        }

        $minPrice = $product->getMinPrice();

        if ($minPrice && end($prices)['price'] >= $minPrice) {
            return;
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
        $stocks = $this->parsedPayload['stocks'] ?? [];

        $toSaveProductStocks = ($this->flags[ProductFlag::SaveStocks->value] ?? false)
            && !empty($stocks);

        if (!$toSaveProductStocks) {
            return;
        }

        $lastStock = end($stocks);
        $productLastStock = $product->getLastStock();

        $isLastStockEqualsQty = $lastStock && $productLastStock
            && $productLastStock->getDateCreated()->format('d.m.Y') === $this->dateService->getDateTime($lastStock['date'])
                ->format('d.m.Y')
            && $productLastStock->getQty() === $lastStock['qty'];

        if ($isLastStockEqualsQty) {
            return;
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
}
