<?php

namespace App\Services;

use App\Models\Cart;

class FreeShippingService
{
    public function isCartEligible(Cart $cart)
    {
        return false;
    }
}
