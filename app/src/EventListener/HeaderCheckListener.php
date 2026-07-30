<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Cache\ShopCacheProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class HeaderCheckListener
{
    public function __construct(
        private ShopCacheProvider $shopCacheProvider,
        #[Autowire('%env(X_REQUESTED_WITH)%')]
        private string $requestedWith,
        #[Autowire('%env(PRICE_CHECKER_ID)%')]
        private string $priceCheckerId,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $shopType = null;
        if ($request) {
            $payload = $request->getPayload();
            $shopType = $payload->get('shop_type');
        }

        $shopId = $this->shopCacheProvider->getShopIdByType($shopType);

        if (empty($shopId)) {
            throw new AccessDeniedHttpException('Missing or not correct required shop type');
        }

        $requestedWith = $request->headers->get('x-requested-with');
        $priceCheckerId = $request->headers->get('t-price-checker-id');

        if (empty($requestedWith) || empty($priceCheckerId)
            || empty($this->requestedWith) || empty($this->priceCheckerId)
            || $requestedWith !== $this->requestedWith
            || $priceCheckerId != $this->priceCheckerId
        ) {
            throw new AccessDeniedHttpException('Missing required headers values');
        }
    }
}
