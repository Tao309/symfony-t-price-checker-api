<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Cache\BookBindingTypesCacheProvider;
use App\Cache\BookPublishingBrandCacheProvider;
use App\Cache\BookPublishingHouseCacheProvider;
use App\Cache\BookSeriesCacheProvider;
use App\Cache\SourceProductTypesCacheProvider;
use App\Enum\ShopType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ConfigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private BookSeriesCacheProvider $bookSeriesCacheProvider,
        private BookPublishingBrandCacheProvider $bookPublishingBrandCacheProvider,
        private BookPublishingHouseCacheProvider $bookPublishingHouseCacheProvider,
        private BookBindingTypesCacheProvider $bindingTypesCacheProvider,
        private SourceProductTypesCacheProvider $sourceProductTypesCacheProvider,
        #[Autowire('%env(URL_WB)%')]
        private readonly string $urlWb,
        #[Autowire('%env(URL_OZON)%')]
        private readonly string $urlOzon,
        #[Autowire('%env(URL_CHITAI_GOROD)%')]
        private readonly string $urlChitaiGorod,
        #[Autowire('%env(URL_FFAN)%')]
        private readonly string $urlFfan,
        #[Autowire('%env(URL_KNIGOFAN)%')]
        private readonly string $urlKnigofan,
        #[Autowire('%env(APP_VERSION)%')]
        private readonly string $appVersion,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->getRequestUri() === '/api') {
            return;
        }

        if (!($request->isMethod(Request::METHOD_GET) || $request->isMethod(Request::METHOD_POST))) {
            return;
        }

        $response = $event->getResponse();

        if (!$response->headers->get('Content-Type')
            || !str_contains($response->headers->get('Content-Type'), 'application/ld+json')
        ) {
            return;
        }

        $data = json_decode($response->getContent(), true) ?? [];

        $data['data'] ??= [];
        $data['data']['config'] = [
            'processed_at' => date('Y-m-d H:i:s'),
            'source_product_types' => $this->sourceProductTypesCacheProvider->get(),
            'book_binding_types' => $this->bindingTypesCacheProvider->get(),
            'book_publishing_houses' => $this->bookPublishingHouseCacheProvider->get(),
            'book_publishing_brands' => $this->bookPublishingBrandCacheProvider->get(),
            'book_series' => $this->bookSeriesCacheProvider->get(),
            'shop_urls' => [
                ShopType::Wildberries->value => $this->urlWb,
                ShopType::Ozon->value => $this->urlOzon,
                ShopType::ChitaiGorod->value => $this->urlChitaiGorod,
                ShopType::Ffan->value => $this->urlFfan,
                ShopType::Knigofan->value => $this->urlKnigofan,
            ],
            'access_rights' => $this->getAccessRights(),
            'app_version' => $this->appVersion ?? 'not-found-version',
        ];

        $response->setContent(json_encode($data));
    }

    private function getAccessRights(): array
    {
        return [
            'product' => [
                'update' => true,
                'create' => true,
                'limit_enabled' => false,
                'limit' => null
            ],
            'book' => [
                'update' => true,
                'create' => true,
                'limit' => null,
                'add_publishing_house' => true,
                'add_publishing_brand' => true,
                'add_series' => true
            ],
            'source_product' => [
                'enabled' => true,
                'create' => true,
                'update' => true,
                'limit' => null
            ],
            'shop' => [
                'list' => [
                    'ozon',
                    'wildberries',
                    'chitai-gorod',
                    'ffan',
                    'knigofan'
                ]
            ]
        ];
    }
}
