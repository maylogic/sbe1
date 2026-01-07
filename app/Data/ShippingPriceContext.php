<?php

namespace App\Data;

use App\Data\Abstract\PriceContext;
use App\Models\Cart;

readonly class ShippingPriceContext extends PriceContext
{
    public function __construct(public Cart $cart)
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        return ['cart' => $this->cart];
    }

    public function key(): string
    {
        return "PriceContext::Cart::{$this->cart->id}";
    }
}
