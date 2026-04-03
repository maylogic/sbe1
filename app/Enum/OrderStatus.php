<?php

namespace App\Enum;

use App\States\Order\CanceledState;
use App\States\Order\CompletedState;
use App\States\Order\DeliveryState;
use App\States\Order\OrderStateInterface;
use App\States\Order\PackingState;
use App\States\Order\PendingState;
use App\States\Order\PrintingState;
use App\Traits\EnumTrait;

enum OrderStatus: string
{
    use EnumTrait;

    case PENDING = 'PENDING';
    case PRINTING = 'PRINTING';
    case PACKING = 'PACKING';
    case DELIVERY = 'DELIVERY';
    case COMPLETED = 'COMPLETED';
    case CANCELED = 'CANCELED';

    public function getStateObject(): OrderStateInterface
    {
        return match ($this) {
            OrderStatus::PENDING => new PendingState(),
            OrderStatus::PRINTING => new PrintingState(),
            OrderStatus::PACKING => new PackingState(),
            OrderStatus::DELIVERY => new DeliveryState(),
            OrderStatus::COMPLETED => new CompletedState(),
            OrderStatus::CANCELED => new CanceledState(),
        };
    }
}
