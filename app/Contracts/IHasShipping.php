<?php

namespace App\Contracts;

use App\Models\CustomerAddress;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface IHasShipping
{
    public function shipping_method(): BelongsTo;

    public function shipping_address(): BelongsTo;

    public function getShippingMethod(): ?ShippingMethod;

    public function getShippingAddress(): ?CustomerAddress;
}
