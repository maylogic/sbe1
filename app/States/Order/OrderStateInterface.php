<?php

namespace App\States\Order;

use App\Models\Order;

interface OrderStateInterface
{
    public function process(Order $order): void;

    public function cancel(Order $order): void;

    public function canBeProcessed(): bool;
}
