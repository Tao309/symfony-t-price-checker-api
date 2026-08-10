<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Product;
use App\Entity\ProductUserData;
use App\Enum\ProductFlag;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ProductDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const string ALREADY_CALLED = 'PRODUCT_DENORMALIZER_ALREADY_CALLED';

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Product::class && !isset($context[self::ALREADY_CALLED]);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;

        $flags = $data['flags'] ?? [];
        $toSaveProductUserData = ($flags[ProductFlag::SaveProductUserData->value] ?? false)
            && !empty($data['product_user_data']);

        if (!$toSaveProductUserData) {
            $context['groups'] = array_values(array_diff($context['groups'], [ProductUserData::GROUP_PUD_WRITE_UPDATE]));
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->denormalizer->getSupportedTypes($format);
    }
}
