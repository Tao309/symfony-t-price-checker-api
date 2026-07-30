<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator as ApiPlatformPaginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\SourceProduct;
use App\Repository\SourceProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements ProviderInterface<SourceProduct[]|SourceProduct|null>
 */
final class SourceProductsSearchProvider extends SearchProvider implements ProviderInterface
{
    public function __construct(
        private SourceProductRepository $sourceProductRepository,
        RequestStack $requestStack,
        public readonly Pagination $pagination
    ) {
        parent::__construct($requestStack, $pagination);
    }

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ?ApiPlatformPaginator {
        [$page, $itemsPerPage, $title] = $this->getConfig($operation, $uriVariables, $context);

        if (!($page && $itemsPerPage)) {
            return null;
        }

        $doctrinePaginator = $this->sourceProductRepository->findWithPagination((int) $page, (int) $itemsPerPage, $title);

        return new ApiPlatformPaginator($doctrinePaginator);
    }
}
