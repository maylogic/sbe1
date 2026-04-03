<?php

namespace App\States\Order;

use App\Enum\OrderStatus;
use App\Models\Order;

class PendingState extends AbstractOrderState implements OrderStateInterface
{
    public function process(Order $order): void
    {
        $order->update(['state' => OrderStatus::PRINTING]);
    }
}
