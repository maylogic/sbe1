<?php

namespace App\States\Order;

use App\Enum\OrderStatus;
use App\Models\Order;

abstract class AbstractOrderState implements OrderStateInterface
{
    public function cancel(Order $order): void
    {
        $order->update(['status' => OrderStatus::CANCELED]);
    }

    public function canBeProcessed(): bool
    {
        return true;
    }
}
