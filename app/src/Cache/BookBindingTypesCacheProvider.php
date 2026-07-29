<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\BookBindingTypeRepository;

class BookBindingTypesCacheProvider extends CommonCacheProvider
{
    protected const string NAME = 't_book_binding_types';
    protected const int EXPIRES = 3600 * 24 * 30;

    public function __construct(
        private readonly BookBindingTypeRepository $bookBindingTypeRepository,
    ) {
        parent::__construct();
    }

    protected function generateData(): array
    {
        return $this->bookBindingTypeRepository->createQueryBuilder('s')
            ->orderBy('s.label', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
