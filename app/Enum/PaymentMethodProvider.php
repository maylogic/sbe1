<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum PaymentMethodProvider: string
{
    use EnumTrait;

    case TBANK = 'tbank';
}
