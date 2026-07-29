<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\BookPublishingBrandRepository;

class BookPublishingBrandCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_book_publishing_brands';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private readonly BookPublishingBrandRepository $bookPublishingBrandRepository,
    ) {
        parent::__construct();
    }

    protected function generateData(): array
    {
        return $this->bookPublishingBrandRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
