<?php

declare(strict_types=1);

namespace App\Enum;

enum ProductFlag: string
{
    case SaveProductUserData = 'flagToSaveProductUserData';
    case SavePrices = 'flagToSavePrices';
    case SaveStocks = 'flagToSaveStocks';
    case ChangeId = 'flagToChangeId';
}
