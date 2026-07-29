<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\BookPublishingHouseRepository;

class BookPublishingHouseCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_book_publishing_houses';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private readonly BookPublishingHouseRepository $bookPublishingHouseRepository,
    ) {
        parent::__construct();
    }

    protected function generateData(): array
    {
        return $this->bookPublishingHouseRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
