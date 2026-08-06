<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SourceProductUserData;
use App\Exception\HasRelationException;
use App\Repository\ProductRepository;
use App\Repository\SourceProductRepository;
use App\Repository\SourceProductUserDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class LinkSourceProductController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly SourceProductUserDataRepository $sourceProductUserDataRepository,
        private readonly ProductRepository $productRepository,
        private readonly SourceProductRepository $sourceProductRepository,
        private Security $security,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(
        int $productId,
        int $sourceProductId,
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

        $sourceProduct = $this->sourceProductRepository->find($sourceProductId);

        if (!$sourceProduct) {
            throw new EntityNotFoundException(\sprintf('Источник товара с ID = "%s" не найден', $sourceProductId));
        }

        $user = $this->security->getUser();

        $spud = $this->sourceProductUserDataRepository->findOneBy([
            'sourceProduct' => $sourceProduct,
            'userCreated' => $user,
        ]);

        if (!$spud) {
            $bud = new SourceProductUserData();
            $bud->setSourceProduct($sourceProduct);
            $bud->setUserCreated($user);
            $this->em->persist($bud);
        }

        $product->setSourceProduct($sourceProduct);

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
