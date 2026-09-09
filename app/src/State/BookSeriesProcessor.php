<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\BookSeriesCacheProvider;
use App\Entity\BookSeries;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<BookSeries, BookSeries>
 */
final readonly class BookSeriesProcessor implements ProcessorInterface
{
    public function __construct(
        private BookSeriesCacheProvider $cacheProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!($operation instanceof Patch || $operation instanceof Post)) {
            return null;
        }

        if ($data instanceof BookSeries) {
            $this->cacheProvider->delete();

            return [
                'entity' => $this->persistProcessor->process($data, $operation, $uriVariables, $context),
            ];
        }

        return null;
    }
}
