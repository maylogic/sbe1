<?php

namespace App\States\Order;

use App\Enum\OrderStatus;
use App\Models\Order;

class PackingState extends AbstractOrderState implements OrderStateInterface
{
    public function process(Order $order): void
    {
        $order->update(['status' => OrderStatus::DELIVERY]);
    }
}
