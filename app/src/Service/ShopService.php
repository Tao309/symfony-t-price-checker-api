<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\ShopType;

class ShopService
{
    private ?int $shopId = null;
    private ?string $shopType = null;

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): static
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getShopType(): ?string
    {
        return $this->shopType;
    }

    public function setShopType(string $shopType): static
    {
        $this->shopType = $shopType;

        return $this;
    }

    public function isWildberriesShopType(): bool
    {
        return $this->getShopType() === ShopType::Wildberries->value;
    }
}
