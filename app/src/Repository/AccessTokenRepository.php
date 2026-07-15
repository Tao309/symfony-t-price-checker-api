<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function findOneByValue(string $value): ?AccessToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('token', $value)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
