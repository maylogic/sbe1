<?php

namespace App\Models;

use App\Contracts\IHasShipping;
use App\Data\Price;
use App\Traits\HasShipping;
use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cart extends Model implements IHasShipping
{
    protected $guarded = [];

    const string SESSION_KEY = 'cart_anon_id';

    use HasFactory;
    use HasShipping;

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0 && $this->coupons()->count() === 0;
    }

    public function isFresherThan(?Cart $other): bool
    {
        if (!$other) {
            return true; // текущая корзина "новее", так как другая отсутствует
        }

        return $this->updated_at > $other->updated_at;
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
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
     * @return Builder<Cart>
     */
    public static function withEverything(): Builder
    {
        return Cart::with([
            'items',
            'coupons',
            'shipping_address',
            'shipping_method',
        ]);
    }

    public function setEmail(string $email): void
    {
        $this->update([
            'email' => $email
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
