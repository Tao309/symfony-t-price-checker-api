<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BookUserData;
use App\Entity\Product;
use App\Exception\HasRelationException;
use App\Repository\BookRepository;
use App\Repository\BookUserDataRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class LinkBookController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly BookUserDataRepository $bookUserDataRepository,
        private readonly ProductRepository $productRepository,
        private readonly BookRepository $bookRepository,
        private Security $security,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(
        int $productId,
        int $bookId,
    ): JsonResponse {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new EntityNotFoundException(\sprintf('Продукт с ID = "%s" не найден', $productId));
        }

        if ($product->getSourceProduct()) {
            throw new HasRelationException('Продукт уже имеет связь с источником товаров');
        }

        if ($product->getBook()) {
            throw new HasRelationException('Продукт уже имеет связь с книгой');
        }

        $book = $this->bookRepository->find($bookId);

        if (!$book) {
            throw new EntityNotFoundException(\sprintf('Книга с ID = "%s" не найдена', $bookId));
        }

        $user = $this->security->getUser();

        $bud = $this->bookUserDataRepository->findOneBy([
            'book' => $book,
            'userCreated' => $user,
        ]);

        if (!$bud) {
            $bud = new BookUserData();
            $bud->setBook($book);
            $bud->setUserCreated($user);
            $this->em->persist($bud);
        }

        $product->setBook($book);

        $this->em->persist($product);
        $this->em->flush();

        $productData = $this->serializer->normalize(
            $this->productRepository->find($productId),
            'json',
            [
                'groups' => [Product::GROUP_PRODUCT_READ],
            ]
        );

        return $this->json([
            'product' => $productData,
        ]);
    }
}
