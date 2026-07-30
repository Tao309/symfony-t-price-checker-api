<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator as ApiPlatformPaginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements ProviderInterface<Book[]|Book|null>
 */
final class BooksSearchProvider implements ProviderInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private BookRepository $bookRepository,
        private readonly Pagination $pagination
    ) {
    }

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ?ApiPlatformPaginator {
        $title = $uriVariables['title'] ?? null;

        if (!$title) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        [$page, , $itemsPerPage] = $this->pagination->getPagination($operation, $context);

        $page = $context['filters']['page'] ?? $page;
        $itemsPerPage = $context['filters']['itemsPerPage'] ?? $itemsPerPage;

        $doctrinePaginator = $this->bookRepository->findWithPagination((int) $page, (int) $itemsPerPage, $title);

        return new ApiPlatformPaginator($doctrinePaginator);
    }
}
