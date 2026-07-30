<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsDecorator(decorates: 'api_platform.hydra.normalizer.collection')]
readonly class HydraMemberRenamerNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        if ($this->normalizer instanceof NormalizerAwareInterface) {
            $this->normalizer->setNormalizer($normalizer);
        }
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {
        $data = $this->normalizer->normalize($data, $format, $context);

        if (\is_array($data)) {
            if (isset($data['totalItems'])) {
                $data['total'] = $data['totalItems'];
                unset($data['totalItems']);
            }
            if (isset($data['member'])) {
                $data['items'] = $data['member'];
                unset($data['member']);
            }
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        $operation = $context['operation'] ?? null;
        if (!$operation instanceof GetCollection) {
            return false;
        }

        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->normalizer->getSupportedTypes($format);
    }
}
