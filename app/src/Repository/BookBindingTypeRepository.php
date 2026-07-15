<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookBindingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookBindingType>
 */
class BookBindingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookBindingType::class);
    }
}
