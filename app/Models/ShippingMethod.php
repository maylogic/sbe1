<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Database\Factories\ShippingMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property int $price
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ShippingMethodFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShippingMethod extends Model implements IHasPrice
{
    /** @use HasFactory<ShippingMethodFactory> */
    use HasFactory;
    use HasPrice;

    public string $estimated_delivery = '3 дня';
}
