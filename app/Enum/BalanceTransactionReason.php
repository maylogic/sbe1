<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum BalanceTransactionReason: string
{
    use EnumTrait;

    case REF = 'REF';
    case SPEND = 'SPEND';
    case WITHDRAW = 'WITHDRAW';
    case ADD = 'ADD';
}
