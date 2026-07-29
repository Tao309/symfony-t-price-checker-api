<?php

declare(strict_types=1);

namespace App\Cache;

interface CacheProviderInterface
{
    public function get(): mixed;

    public function delete(): void;
}
