<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum ShippingMethodProvider: string
{
    use EnumTrait;

    case CDEK = 'CDEK';
    case BOXBERRY = 'BOXBERRY';
    case POST = 'POST';
    case POST_FIRST_CLASS = 'POST_FIRST_CLASS';
}
