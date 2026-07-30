<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class SearchProvider
{
    public function __construct(
        private RequestStack $requestStack,
        private readonly Pagination $pagination
    ) {
    }

    protected function getConfig(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ?array {
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

        return [$page, $itemsPerPage, $title];
    }
}
