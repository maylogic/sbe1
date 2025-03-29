<?php

namespace App\Services;

use App\Data\Totals;
use App\Models\Cart;

readonly class TotalsService
{
    public function __construct(private PriceService $priceService)
    {
    }

    public function getCartTotals(Cart $cart): Totals
    {
        $subtotal = $cart->subtotal();
        $deliveryPrice = $this->priceService->getCartShippingPrice($cart);
        $grandTotalVal = $subtotal + $deliveryPrice?->regular;
        if ($cart->apply_balance) {
            
        }
        $grandTotal = $this->priceService->getPrice($grandTotalVal);
        return new Totals($subtotal, $grandTotal);
    }
}
