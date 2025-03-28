<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string|null $anon_id
 * @property int $user_id
 * @property int|null $shipping_method_id
 * @property int|null $billing_address_id
 * @property int|null $shipping_address_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CustomerAddress|null $billing_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionItem> $option_items
 * @property-read int|null $option_items_count
 * @property-read \App\Models\ProductSize|null $product_size
 * @property-read \App\Models\CustomerAddress|null $shipping_address
 * @property-read \App\Models\ShippingMethod|null $shipping_method
 * @method static \Database\Factories\CartFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereAnonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereBillingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereShippingMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 * @mixin \Eloquent
 */
class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function shipping_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function shipping_method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function option_items(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionItem::class, 'cart_option_item');
    }

    public function product_size(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }
}
