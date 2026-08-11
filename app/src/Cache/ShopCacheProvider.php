<?php

declare(strict_types=1);

namespace App\Cache;

use App\Entity\Shop;
use App\Repository\ShopRepository;

class ShopCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_shops';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private ShopRepository $shopRepository,
    ) {
        parent::__construct();
    }

    public function getShopIdByType(?string $shopType): ?int
    {
        if (!$shopType) {
            return null;
        }

        $validShopId = null;

        foreach ($this->get() as $shopData) {
            if ($shopType && $shopData['type'] === $shopType) {
                $validShopId = $shopData['id'];
                break;
            }
        }

        return $validShopId;
    }

    public function getShopByType(?string $shopType): ?Shop
    {
        if (!$shopType) {
            return null;
        }

        $validShopData = null;

        foreach ($this->get() as $shopData) {
            if ($shopType && $shopData['type'] === $shopType) {
                $validShopData = $shopData;
                break;
            }
        }

        if (!$validShopData) {
            return null;
        }

        return new Shop()
            ->setId($validShopData['id'])
            ->setDomain($validShopData['domain'])
            ->setType($validShopData['type'])
            ->setUrl($validShopData['url'])
        ;
    }

    protected function generateData(): array
    {
        return $this->shopRepository->findAllAsArray();
    }
}
