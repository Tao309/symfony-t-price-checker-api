<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Product;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security
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

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if (Product::class !== $resourceClass) {
            return;
        }

        $joins = $queryBuilder->getDQLPart('join');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (!isset($joins[$rootAlias])) {
            return;
        }

        $filterFields = [
            'bookUserData',
            'sourceProductUserData',
        ];

        foreach ($joins[$rootAlias] as $key => $joinPart) {
            $joinAlias = $joinPart->getAlias();

            [, $field] = explode('.', $joinPart->getJoin());

            if (!\in_array($field, $filterFields)) {
                continue;
            }

            $joins[$rootAlias][$key] = new Join(
                joinType: 'LEFT',
                join: $joinPart->getJoin(),
                alias: $joinAlias,
                conditionType: Join::WITH,
                condition: \sprintf('%s.userCreated = %s', $joinAlias, $user->getId())
            );
        }

        $joins[$rootAlias] = array_values($joins[$rootAlias]);
        $queryBuilder->add('join', $joins);
    }
}
