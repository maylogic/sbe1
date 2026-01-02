<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Enum\ShippingMethodProvider;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMethod extends Model implements IHasPrice
{
    use HasFactory;
    use HasPrice;
    use SoftDeletes;

    public string $estimated_delivery = '3 дня';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'provider' => ShippingMethodProvider::class
        ];
    }
}
