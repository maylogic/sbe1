<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum OrderStatus: string
{
    use EnumTrait;

    case PENDING = 'PENDING';
    case PRINTING = 'PRINTING';
    case PACKING = 'PACKING';
    case DELIVERY = 'DELIVERY';
    case COMPLETED = 'COMPLETED';
}
