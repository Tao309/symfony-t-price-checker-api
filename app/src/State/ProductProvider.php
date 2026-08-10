<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
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

        $user = $this->security->getUser();

        if (!$user) {
            return null;
        }

        if ($operation instanceof CollectionOperationInterface) {
            $context['filters'] ??= [];

            $ids = $request->query->get('ids');

            if (empty($ids)) {
                return null;
            }

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

        if (isset($uriVariables['id'])
            && (
                $operation instanceof Get
                || $operation instanceof Patch
                || $operation instanceof Post
            )
        ) {
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
        $product->getPrices()->clear();

        foreach ($prices as $price) {
            $product->addPrice($price);
        }

        $stocks = $product->getStocks()->matching($criteria);
        $product->getStocks()->clear();

        foreach ($stocks as $stock) {
            $product->addStock($stock);
        }
    }
}
