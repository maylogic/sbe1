<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public array $cache = [];

    public function getCurrentCart(): Cart
    {
        $userId = Auth::user()?->id;
        $cartAnonId = session(Cart::SESSION_KEY);
        $cacheKey = $userId ? 'uid-' . $userId : 'anon-' . $cartAnonId;
        if (!array_key_exists($cacheKey, $this->cache)) {
            $this->cache[$cacheKey] = $this->getOrCreateCart();
        }
        return $this->cache[$cacheKey];
    }

    public function addCartItem(int $productId, int $quantity): void
    {
        $cart = $this->getCurrentCart();
        $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
        $cart->load('items');
    }

    public function removeCartItemId(int $cartItemId): void
    {
        $this->getCurrentCart()->removeCartItem($cartItemId);
    }

    public function isCouponApplied(Coupon $coupon): bool
    {
        return $this->getCurrentCart()->isCouponApplied($coupon);
    }

    public function applyCoupon(Coupon $coupon): void
    {
        $this->getCurrentCart()->applyCoupon($coupon);
    }

    public function removeCoupon(Coupon $coupon): void
    {
        $this->getCurrentCart()->removeCoupon($coupon);
    }

    public function canApplyBalance(Cart $cart): bool
    {
        $user = Auth::user();
        if ($user) {
            return $cart->user_id === $user->id
                && $user->getBalance()->value > 0;
        }
        return false;
    }

    private function getOrCreateCart(): Cart
    {
        // retrieve or create user cart
        $userId = Auth::user()?->id;
        if ($userId) {
            return Cart::withEverything()
                ->where('user_id', '=', $userId)
                ->firstOr(function () use ($userId) {
                    return Cart::factory()->create([
                        'user_id' => $userId
                    ]);
                });
        }
        // retrieve or create cart for existing anon key
        $cartAnonId = session(Cart::SESSION_KEY);
        if ($cartAnonId) {
            return Cart::withEverything()
                ->where('anon_id', '=', $cartAnonId)
                ->firstOr(function () use ($cartAnonId) {
                    return Cart::factory()->create([
                        'anon_id' => $cartAnonId
                    ]);
                });
        }
        // create new anon key and cart
        $cartAnonId = uuid_create();
        session([Cart::SESSION_KEY => $cartAnonId]);
        return Cart::factory()->create([
            'anon_id' => $cartAnonId
        ]);
    }
}
