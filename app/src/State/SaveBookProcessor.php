<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Book;
use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<Product, Product>
 */
final class SaveBookProcessor implements ProcessorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {
    }

    /**
     * @throws \RequestParseBodyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!($operation instanceof Patch || $operation instanceof Post)) {
            return null;
        }

        if ($data instanceof Book) {
            $book = $this->saveAction($data, $operation);

            return [
                'entity' => $this->persistProcessor->process($book, $operation, $uriVariables, $context),
            ];
        }

        return null;
    }

    private function saveAction(Book $book, Operation $operation): Book
    {
        $this->addBookUserData($book);
        $book->setUserCreated($this->security->getUser());

        $this->validator->validate($book, $operation->getValidationContext());

        return $book;
    }

    private function addBookUserData(Book $book): void
    {
        $bud = $book->getBookUserData();
        $bud->setUserCreated($this->security->getUser());
        $bud->setBook($book);
    }
}
