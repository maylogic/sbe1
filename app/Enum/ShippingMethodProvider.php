<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum ShippingMethodProvider: string
{
    use EnumTrait;

    case CDEK = 'cdek';
    case BOXBERRY = 'boxberry';
    case POST = 'post';
    case POST_FIRST_CLASS = 'post_first_class';
}
