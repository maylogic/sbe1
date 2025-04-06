<?php

namespace App\Models;

use App\Contracts\IHasShipping;
use App\Traits\HasShipping;
use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 *
 *
 * @property int $id
 * @property string|null $anon_id
 * @property int $apply_balance
 * @property int|null $user_id
 * @property int|null $shipping_method_id
 * @property int|null $billing_address_id
 * @property int|null $shipping_address_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CustomerAddress|null $billing_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\CustomerAddress|null $shipping_address
 * @property-read \App\Models\ShippingMethod|null $shipping_method
 * @method static \Database\Factories\CartFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereAnonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereApplyBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereBillingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereShippingMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 * @mixin \Eloquent
 */
class Cart extends Model implements IHasShipping
{
    protected $fillable = ['apply_balance', 'anon_id'];

    const SESSION_KEY = 'cart_anon_id';

    /** @use HasFactory<CartFactory> */
    use HasFactory;
    use HasShipping;

    public function subtotal(): int
    {
        return $this->items()
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->sum(DB::raw('products.price * cart_items.quantity'));
    }

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

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'cart_coupon');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Cart>
     */
    public static function withEverything(): \Illuminate\Database\Eloquent\Builder
    {
        return Cart::with([
            'items',
            'coupons',
            'billing_address',
            'shipping_address',
            'shipping_method',
        ]);
    }

    public function applyCoupon(Coupon $coupon): void
    {
        DB::transaction(function () use ($coupon) {
            $c = Coupon::whereCode($coupon->code)->lockForUpdate()->firstOrFail();
            if ($c->quantity !== null && $c->quantity <= 0) {
                throw new \InvalidArgumentException('Купон закончился');
            }
            $this->coupons()->attach($coupon->id);
            $this->load(['coupons']);
            if ($c->quantity !== null) {
                $c->update(['quantity', $c->quantity - 1]);
            }
        });
    }

    public function removeCoupon(Coupon $coupon): void
    {
        DB::transaction(function () use ($coupon) {
            $c = Coupon::whereCode($coupon->code)->lockForUpdate()->firstOrFail();
            $this->coupons()->detach($coupon->id);
            $this->load(['coupons']);
            if ($c->quantity !== null) {
                $c->update(['quantity', $c->quantity + 1]);
            }
        });
    }

    public function removeCartItem(int $cartItemId): void
    {
        $this->items->find($cartItemId)?->delete();
        $this->load(['items']);
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): void
    {
        $this->items->find($cartItemId)?->update(['quantity' => $quantity]);
        $this->load(['items']);
    }

    public function isCouponApplied(Coupon $coupon): bool
    {
        return $this->coupons()
            ->where('code', '=', $coupon->code)
            ->exists();
    }
}
