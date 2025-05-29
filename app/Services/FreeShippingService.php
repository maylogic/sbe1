<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;

class FreeShippingService
{
    public function isCartEligible(Cart $cart)
    {
        return false;
    }

    public function isOrderEligible(Order $order)
    {
        return false;
    }
}
