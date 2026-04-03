<?php

namespace App\Models;

use App\Contracts\IHasShipping;
use App\Enum\OrderStatus;
use App\Traits\HasShipping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model implements IHasShipping
{
    protected $guarded = [];

    use HasFactory;
    use HasShipping;

    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function process(): void
    {
        $this->status->getStateObject()->process($this);
    }

    public function cancel(): void
    {
        $this->status->getStateObject()->cancel($this);
    }

    protected function canBeProcessed(): Attribute
    {
        return Attribute::get(fn() => $this->status->getStateObject()->canBeProcessed());
    }

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
     * @return Builder<Order>
     */
    public static function withEverything(): Builder
    {
        return Order::with([
            'items',
            'coupons',
            'shipping_address',
            'shipping_method',
        ]);
    }
}
