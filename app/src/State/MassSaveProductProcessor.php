<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\MassSaveProductInputDto;
use App\Entity\Product;
use App\Entity\ProductUserData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MassSaveProductProcessor implements ProcessorInterface
{
    public function __construct(
        private IriConverterInterface $iriConverter,
        private SerializerInterface $serializer,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param MassSaveProductInputDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof MassSaveProductInputDto) {
            return null;
        }

        $errorMessage = [];
        $savedProduct = [];
        $errorsCount = 0;

        foreach ($data->products as $itemData) {
            try {
                $groups = [Product::GROUP_UPDATE, ProductUserData::GROUP_UPDATE];

                if (!empty($itemData['id'])) {
                    $itemData['id'] = $this->iriConverter->getIriFromResource(
                        resource: Product::class,
                        context: ['uri_variables' => ['id' => $itemData['id']]]
                    );

                    $groups = [Product::GROUP_CREATE, ProductUserData::GROUP_CREATE];
                }

                $savedProduct[] = $product = $this->serializer->denormalize(
                    $itemData,
                    Product::class,
                    'json',
                    [
                        'groups' => $groups,
                    ]
                );

                $this->em->persist($product);
                $this->em->flush();
            } catch (\Throwable $e) {
                ++$errorsCount;
                $errorMessage[] = $e->getMessage();
            }
        }

        if (!\count($savedProduct)) {
            throw new \Exception('Ничего не сохранилось');
        }

        $result = [
            'products_count' => \count($data->products),
            'errors_count' => $errorsCount,
            'saved_count' => \count($savedProduct),
            'products' => $savedProduct,
        ];

        $result['message'] = $errorMessage ? implode('. ', array_unique($errorMessage)) : 'Products are saved';

        return $result;
    }
}
