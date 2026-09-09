<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\BookPublishingHouseCacheProvider;
use App\Entity\BookPublishingHouse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<BookPublishingHouse, BookPublishingHouse>
 */
final readonly class BookPublishingHouseProcessor implements ProcessorInterface
{
    public function __construct(
        private BookPublishingHouseCacheProvider $cacheProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!($operation instanceof Patch || $operation instanceof Post)) {
            return null;
        }

        if ($data instanceof BookPublishingHouse) {
            $this->cacheProvider->delete();

            return [
                'entity' => $this->persistProcessor->process($data, $operation, $uriVariables, $context),
            ];
        }

        return null;
    }
}
