<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @implements ProcessorInterface<Book, Book>
 */
readonly class WrapEntityProcessor implements ProcessorInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        if (!$operation instanceof Patch) {
            return $result;
        }

        return [
            'entity' => $result,
        ];
    }
}
