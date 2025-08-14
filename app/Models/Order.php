<?php

namespace App\Models;

use App\Contracts\IHasShipping;
use App\Enum\OrderStatus;
use App\Traits\HasShipping;
use Database\Factories\OrderFactory;
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
 * @property string|null $email
 * @property string|null $comment
 * @property OrderStatus $status
 * @property int $apply_balance
 * @property int|null $user_id
 * @property int $shipping_method_id
 * @property int $shipping_address_id
 * @property int $shipping_price_regular
 * @property int $shipping_price_discounted
 * @property int $subtotal
 * @property int $grand_total_regular
 * @property int $grand_total_discounted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\CustomerAddress $shipping_address
 * @property-read \App\Models\ShippingMethod $shipping_method
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAnonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereApplyBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereGrandTotalDiscounted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereGrandTotalRegular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingPriceDiscounted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingPriceRegular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model implements IHasShipping
{
    protected $fillable = [
        'apply_balance',
        'anon_id',
        'email',
        'comment',
        'shipping_price_regular',
        'shipping_price_discounted',
        'subtotal',
        'grand_total_regular',
        'grand_total_discounted',
    ];

    /** @use HasFactory<OrderFactory> */
    use HasFactory;
    use HasShipping;

    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function getRegularOrDiscountedGrandTotal(): int
    {
        return $this->grand_total_discounted ?? $this->grand_total_regular;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipping_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function shipping_method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'order_coupon');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Order>
     */
    public static function withEverything(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::with([
            'items',
            'coupons',
            'shipping_address',
            'shipping_method',
        ]);
    }
}
