<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\SourceProductTypeRepository;

class SourceProductTypesCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_source_product_types';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private readonly SourceProductTypeRepository $sourceProductTypeRepository,
    ) {
        parent::__construct();
    }

    protected function generateData(): array
    {
        return $this->sourceProductTypeRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
