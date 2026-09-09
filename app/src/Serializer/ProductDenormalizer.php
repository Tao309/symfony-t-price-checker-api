<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Patch;
use App\Entity\Product;
use App\Entity\ProductUserData;
use App\Entity\Shop;
use App\Enum\ProductFlag;
use App\Service\ShopService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const string ALREADY_CALLED = 'PRODUCT_DENORMALIZER_ALREADY_CALLED';

    private bool $isNew = true;
    private array $flags = [];

    public function __construct(
        private Security $security,
        private ShopService $shopService,
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Product::class && !isset($context[self::ALREADY_CALLED]);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;

        $this->flags = $data['flags'] ?? [];
        $this->isNew = empty($data['id']);

        $operation = $context['operation'] ?? null;
        $context['groups'] ??= [];
        $isPatch = $operation instanceof Patch;

        $toSaveProductUserData = ($this->flags[ProductFlag::SaveProductUserData->value] ?? false)
            && !empty($data['productUserData']);

        if (!$toSaveProductUserData) {
            $removeGroup = $isPatch ? ProductUserData::GROUP_UPDATE : ProductUserData::GROUP_CREATE;

            $context['groups'] = array_values(array_diff($context['groups'], [$removeGroup]));
        }

        $data['productUserData']['user'] = $this->security->getUser()->getId();

        if (!$this->isNew) {
            $data['productUserData']['product'] = $data['id'];
        }

        $data['shop'] = $this->iriConverter->getIriFromResource(
            resource: Shop::class,
            context: ['uri_variables' => ['id' => $this->shopService->getShop()->getId()]]
        );

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->denormalizer->getSupportedTypes($format);
    }
}
