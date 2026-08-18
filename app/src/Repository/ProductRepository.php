<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Service\ShopService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        private ShopService $shopService,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findByShopProductId(string $shopProductId): ?Product
    {
        return $this->findOneBy([
            'shop' => $this->shopService->getShop()->getId(),
            'shopProductId' => $shopProductId,
        ]);
    }
}
