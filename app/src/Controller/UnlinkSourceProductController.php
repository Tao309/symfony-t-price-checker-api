<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Exception\HasRelationException;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class UnlinkSourceProductController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
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

        if (!$product->getSourceProduct()) {
            throw new HasRelationException('Продукт не имеет источник товара, не требуется отвязка');
        }

        $product->setSourceProduct(null);

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
