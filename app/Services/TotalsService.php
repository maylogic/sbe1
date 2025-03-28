<?php

namespace App\Services;

use App\Data\Price;
use App\Data\Totals;
use App\Models\Cart;

class TotalsService
{
    public function getCartTotals(Cart $cart): Totals
    {
        $subtotal = $cart->items()->sum('product.price');
        $grandTotal = new Price($subtotal, 0, $subtotal);
        return new Totals($subtotal, $grandTotal);
    }
}
