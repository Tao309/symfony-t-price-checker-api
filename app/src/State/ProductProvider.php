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

/**
 * @implements ProviderInterface<Product[]|Product|null>
 */
final class ProductProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProviderInterface $collectionProvider,
        private readonly ProviderInterface $itemProvider,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $result = $this->collectionProvider->provide($operation, $uriVariables, $context);

            // @todo Проверить логику фильтрации на prices, stocks
//            foreach ($result->getIterator() as $product) {
//                $this->filterProductPricesAndStocks($product);
//            }

            return $result;
        }

        if ($operation instanceof Get && isset($uriVariables['id'])) {
            /**
             * @var Product $result
             */
            $result = $this->itemProvider->provide($operation, $uriVariables, $context);
            $this->filterProductPricesAndStocks($result);

            return $result;
        }

        return null;
    }

    private function filterProductPricesAndStocks(Product $product): void
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
