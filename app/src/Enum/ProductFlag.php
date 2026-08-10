<?php

declare(strict_types=1);

namespace App\Enum;

enum ProductFlag: string
{
    case SaveProductUserData = 'flag_to_save_product_user_data';
    case SavePrices = 'flag_to_save_prices';
    case SaveStocks = 'flag_to_save_stocks';
    case ChangeId = 'flag_to_change_id';
}
