<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Post;
use App\Entity\Book;
use App\Entity\BookUserData;
use App\Service\ShopService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class BookDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const string ALREADY_CALLED = 'BOOK_DENORMALIZER_ALREADY_CALLED';

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
        return $type === Book::class && !isset($context[self::ALREADY_CALLED]);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;

        $operation = $context['operation'] ?? null;
        $context['groups'] ??= [];
        $this->isNew = $operation instanceof Post && $operation->getName() === Book::ACTION_CREATE;

        if (empty($data['bookUserData'])) {
            $removeGroup = $this->isNew ? BookUserData::GROUP_CREATE : BookUserData::GROUP_UPDATE;

            $context['groups'] = array_values(array_diff($context['groups'], [$removeGroup]));
        }

        $data['bookUserData']['user'] = $this->security->getUser()->getId();

        if (!$this->isNew) {
            $data['bookUserData']['book'] = $data['id'];
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->denormalizer->getSupportedTypes($format);
    }
}
