<?php

declare(strict_types=1);

namespace App\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

abstract class CommonCacheProvider implements CacheProviderInterface
{
    protected const string NAME = '';
    protected const int EXPIRES = 0;

    abstract protected function generateData(): array;

    private FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    public function get(): array
    {
        $result = $this->cache->get(static::NAME, function (ItemInterface $item): string {
            $item->expiresAfter(static::EXPIRES);

            return json_encode($this->generateData());
        });

        return json_decode($result, true);
    }

    public function delete(): void
    {
        $this->cache->delete(static::NAME);
    }
}
