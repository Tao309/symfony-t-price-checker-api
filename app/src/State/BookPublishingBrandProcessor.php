<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\BookPublishingBrandCacheProvider;
use App\Entity\BookPublishingBrand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<BookPublishingBrand, BookPublishingBrand>
 */
final readonly class BookPublishingBrandProcessor implements ProcessorInterface
{
    public function __construct(
        private BookPublishingBrandCacheProvider $cacheProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!($operation instanceof Patch || $operation instanceof Post)) {
            return null;
        }

        if ($data instanceof BookPublishingBrand) {
            $this->cacheProvider->delete();

            return [
                'entity' => $this->persistProcessor->process($data, $operation, $uriVariables, $context),
            ];
        }

        return null;
    }
}
