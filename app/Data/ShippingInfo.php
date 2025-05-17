<?php

namespace App\Data;

class ShippingInfo
{
    public function __construct(public float $price, public int $days)
    {
    }
}
