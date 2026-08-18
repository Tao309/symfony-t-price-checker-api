<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Dto\ResponseDto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DataWrapperNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'DATA_WRAPPER_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $request = $this->requestStack->getCurrentRequest();
        $data = $this->normalizer->normalize($data, $format, $context);

        if ($request->getRequestUri() === '/api') {
            return $data;
        }

        if (\is_array($data)) {
            $response = new ResponseDto(
                success: true,
                message: 'Success',
            );

            if (isset($data[ResponseDto::MESSAGE])) {
                $response->setMessage($data[ResponseDto::MESSAGE]);
                unset($data[ResponseDto::MESSAGE]);
            }

            if (isset($data[ResponseDto::SUCCESS])) {
                $response->setSuccess($data[ResponseDto::SUCCESS]);
                unset($data[ResponseDto::SUCCESS]);
            }

            $response->setData($data);

            return $response->toArray();
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return \in_array($format, ['json', 'jsonld']);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->normalizer->getSupportedTypes($format);
    }
}
