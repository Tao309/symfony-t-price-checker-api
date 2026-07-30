<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Product;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements ProviderInterface<Product[]|Product|null>
 */
final class ProductProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProviderInterface $collectionProvider,
        private readonly ProviderInterface $itemProvider,
        private Security $security,
        private RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $payload = $request->getPayload();
        $shopType = $payload->get('shop_type');

        if (!$shopType) {
            return null;
        }

        $user = $this->security->getUser();

        if (!$user) {
            return null;
        }

        $context['filters'] ??= [];

        if ($operation instanceof CollectionOperationInterface) {
            $ids = $payload->get('ids');
            $shopProductIds = explode(',', $ids);
            if (empty($shopProductIds)) {
                return null;
            }

            $context['filters']['shopProductId'] = $shopProductIds;

            $result = $this->collectionProvider->provide($operation, $uriVariables, $context);

            foreach ($result->getIterator() as $product) {
                $this->filterPricesAndStocks($product);
            }

            return $result;
        }

        if ($operation instanceof Get && isset($uriVariables['id'])) {
            /**
             * @var Product $result
             */
            $result = $this->itemProvider->provide($operation, $uriVariables, $context);

            $this->filterPricesAndStocks($result);

            return $result;
        }

        return null;
    }

    private function filterPricesAndStocks(Product $product): void
    {
        $user = $this->security->getUser();

        if (!$this->security->isGranted('ROLE_USER')) {
            $user = null;
        }

        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('userCreated', $user));

        $prices = $product->getPrices()->matching($criteria);

        foreach ($product->getPrices() as $price) {
            $product->removePrice($price);
        }

        foreach ($prices as $price) {
            $product->addPrice($price);
        }

        $stocks = $product->getStocks()->matching($criteria);

        foreach ($product->getStocks() as $stock) {
            $product->removeStock($stock);
        }

        foreach ($stocks as $stock) {
            $product->addStock($stock);
        }
    }
}
