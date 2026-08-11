<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Cache\ShopCacheProvider;
use App\Service\ShopService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class HeaderCheckListener
{
    public function __construct(
        private ShopService $shopService,
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

        if ($request->getRequestUri() === '/api') {
            return;
        }

        $shopType = $request->headers->get('shop-type');
        $requestedWith = $request->headers->get('x-requested-with');
        $priceCheckerId = $request->headers->get('t-price-checker-id');

        $shop = $this->shopCacheProvider->getShopByType($shopType);

        if (empty($shop)) {
            throw new AccessDeniedHttpException('Missing or not correct required shop type');
        }

        if (empty($requestedWith) || empty($priceCheckerId)
            || empty($this->requestedWith) || empty($this->priceCheckerId)
            || $requestedWith !== $this->requestedWith
            || $priceCheckerId != $this->priceCheckerId
        ) {
            throw new AccessDeniedHttpException('Missing required headers values');
        }

        $this->shopService->setShop($shop);
    }
}
