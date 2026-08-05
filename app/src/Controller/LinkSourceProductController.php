<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\HasRelationException;
use App\Repository\ProductRepository;
use App\Repository\SourceProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class LinkSourceProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SourceProductRepository $sourceProductRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(
        int $sourceProductId,
        int $productId,
    ): JsonResponse {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new EntityNotFoundException(\sprintf('Продукт с ID = "%s" не найден', $productId));
        }

        if ($product->getSourceProduct()) {
            throw new HasRelationException('Продукт уже имеет связь с источником товаров');
        }

        $sourceProduct = $this->sourceProductRepository->find($sourceProductId);

        if (!$sourceProduct) {
            throw new EntityNotFoundException(\sprintf('Источник товара с ID = "%s" не найден', $sourceProductId));
        }

        $product->setSourceProduct($sourceProduct);

        $this->em->persist($product);
        $this->em->flush();

        return $this->json([
            'product' => $product,
        ]);
    }
}
