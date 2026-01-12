<?php

namespace App\Models;

use App\Contracts\IHasShipping;
use App\Traits\HasShipping;
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

    public function scopeForMerging(): Builder
    {
        return self::with([
            'items' => fn($q) => $q->withPersonalization(),
            'coupons',
        ]);
    }

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

    public function setEmail(string $email): void
    {
        $this->update([
            'email' => $email
        ]);
    }

    public function applyCouponIfNotApplied(Coupon $coupon): void
    {
        if ($this->isCouponApplied($coupon)) {
            throw new \DomainException('Купон уже применён');
        }

        DB::transaction(function () use ($coupon) {
            $c = Coupon::where('id', $coupon->id)->lockForUpdate()->firstOrFail();
            if ($c->quantity !== null && $c->quantity <= 0) {
                throw new \InvalidArgumentException('Купон закончился');
            }
            $this->coupons()->attach($coupon->id);
            if ($c->quantity !== null) {
                $c->decrement('quantity');
            }
        });
    }

    public function removeCouponIfApplied(Coupon $coupon): void
    {
        if (!$this->isCouponApplied($coupon)) {
            throw new \DomainException('Купон не применён к корзине.');
        }

        DB::transaction(function () use ($coupon) {
            $c = Coupon::where('id', $coupon->id)->lockForUpdate()->firstOrFail();
            $this->coupons()->detach($coupon->id);
            if ($c->quantity !== null) {
                $c->increment('quantity');
            }
        });
    }

    public function removeCartItem(int $cartItemId): void
    {
        $this->items()->where('id', $cartItemId)->delete();
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->items()->where('id', $cartItemId)->delete();
        } else {
            $this->items()->where('id', $cartItemId)->update(['quantity' => $quantity]);
        }
    }

    public function isCouponApplied(Coupon $coupon): bool
    {
        return $this->coupons()->where('id', $coupon->id)->exists();
    }
}
