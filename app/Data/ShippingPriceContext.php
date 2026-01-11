<?php

namespace App\Data;

use App\Data\Abstract\PriceContext;
use App\Models\Cart;

readonly class ShippingPriceContext extends PriceContext
{
    public function __construct(public Cart $cart, public Price $cartSubtotal)
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        return ['cart' => $this->cart, 'cart_subtotal' => $this->cartSubtotal];
    }

    public function key(): string
    {
        return "PriceContext::Cart::{$this->cart->id}";
    }
}
