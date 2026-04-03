<?php

namespace App\States\Order;

use App\Models\Order;

class CompletedState extends AbstractOrderState implements OrderStateInterface
{
    public function process(Order $order): void
    {
        throw new \LogicException('Completed order cannot be processed');
    }

    public function cancel(Order $order): void
    {
        throw new \LogicException('Completed order cannot be cancelled');
    }

    public function canBeProcessed(): bool
    {
        return false;
    }
}
