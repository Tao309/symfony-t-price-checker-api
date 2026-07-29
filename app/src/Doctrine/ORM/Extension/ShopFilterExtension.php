<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Cache\ShopCacheProvider;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ShopFilterExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
        private ShopCacheProvider $shopCacheProvider,
        private RequestStack $requestStack,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if (Product::class !== $resourceClass) {
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];

        $shopType = null;
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $payload = $request->getPayload();
            $shopType = $payload->get('shop_type');
        }

        $validShopId = null;

        foreach ($this->shopCacheProvider->get() as $shopData) {
            if ($shopType && $shopData['type'] === $shopType) {
                $validShopId = $shopData['id'];
                break;
            }
        }

        $qb->andWhere(\sprintf('%s.shop = %s', $rootAlias, $validShopId));
    }
}
