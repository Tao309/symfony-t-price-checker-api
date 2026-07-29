<?php

declare(strict_types=1);

namespace App\Cache;

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

    protected function generateData(): array
    {
        return $this->shopRepository->findAllAsArray();
    }
}
