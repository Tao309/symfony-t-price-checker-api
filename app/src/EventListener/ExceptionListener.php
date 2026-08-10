<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Dto\ResponseDto;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', priority: 10)]
class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $data = new ResponseDto(
            message: $exception->getMessage(),
            trace: $exception->getTrace(),
            previousTrace: $exception->getPrevious()?->getTrace() ?? [],
        );

        $event->setResponse(new JsonResponse($data->toArray(true), $statusCode));
    }
}
