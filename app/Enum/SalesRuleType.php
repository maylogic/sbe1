<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum SalesRuleType: string
{
    use EnumTrait;

    case CART = 'CART';
    case PRODUCT = 'PRODUCT';
    case SHIPPING = 'SHIPPING';
}
