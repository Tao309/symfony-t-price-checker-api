<?php

declare(strict_types=1);

namespace App\Enum;

enum ShopType: string
{
    case Ozon = 'ozon';
    case Wildberries = 'wildberries';
    case ChitaiGorod = 'chitai-gorod';
    case Ffan = 'ffan';
    case Knigofan = 'knigofan';
}
