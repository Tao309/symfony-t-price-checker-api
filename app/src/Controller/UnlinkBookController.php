<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\HasRelationException;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UnlinkBookController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(
        int $productId,
    ): JsonResponse {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new EntityNotFoundException(\sprintf('Продукт с ID = "%s" не найден', $productId));
        }

        if (!$product->getBook()) {
            throw new HasRelationException('Продукт не привязан к книге, не требуется отвязка');
        }

        $product->setBook(null);

        $this->em->persist($product);
        $this->em->flush();

        return $this->json([
            'product' => $product,
        ]);
    }
}
