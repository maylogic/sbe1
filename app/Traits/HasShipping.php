<?php

namespace App\Traits;

use App\Models\CustomerAddress;
use App\Models\ShippingMethod;

trait HasShipping
{
    public function getShippingMethod(): ?ShippingMethod
    {
        return $this->shipping_method;
    }

    public function getShippingAddress(): ?CustomerAddress
    {
        return $this->shipping_address;
    }
}
