<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Shop;
use App\Enum\ShopType;

class ShopService
{
    private ?Shop $shop = null;

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function isWildberriesShopType(): bool
    {
        return $this->getShop()->getType() === ShopType::Wildberries->value;
    }
}
