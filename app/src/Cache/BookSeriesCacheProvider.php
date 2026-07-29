<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\BookSeriesRepository;

class BookSeriesCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_book_series';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private readonly BookSeriesRepository $bookSeriesRepository,
    ) {
        parent::__construct();
    }

    protected function generateData(): array
    {
        return $this->bookSeriesRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
