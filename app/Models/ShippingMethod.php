<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Enum\ShippingMethodProvider;
use App\Traits\HasPrice;
use Database\Factories\ShippingMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property int $price
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property ShippingMethodProvider $provider
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\ShippingMethodFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod withoutTrashed()
 * @mixin \Eloquent
 */
class ShippingMethod extends Model implements IHasPrice
{
    /** @use HasFactory<ShippingMethodFactory> */
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
