<?php

namespace App\Services;

use App\Data\Input\PersonalizeInput;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemTextLine;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartService
{
    public array $cache = [];

    public function getCurrentCart($create = true): Cart
    {
        $userId = Auth::user()?->id;
        $cartAnonId = session(Cart::SESSION_KEY);
        $cacheKey = $userId ? 'uid-' . $userId : 'anon-' . $cartAnonId;
        if (!array_key_exists($cacheKey, $this->cache)) {
            $this->cache[$cacheKey] = $this->getOrCreateCart();
        }
        return $this->cache[$cacheKey];
    }

    public function getCurrentCartItemsCount(): int
    {
        $userId = Auth::user()?->id;
        $cartAnonId = session(Cart::SESSION_KEY);
        if (empty($userId) && empty($cartAnonId)) {
            return 0;
        }
        return DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where(function ($query) use ($userId, $cartAnonId) {
                if ($userId) {
                    $query->where('carts.user_id', $userId);
                } else {
                    $query->where('carts.anon_id', $cartAnonId);
                }
            })
            ->count('cart_items.id');
    }

    public function updateShippingAddress(int $customerAddressId): Cart
    {
        $cart = $this->getCurrentCart();
        $cart->update([
            'shipping_address_id' => $customerAddressId,
            'shipping_method_id' => null
        ]);
        $cart->load(['shipping_address']);
        return $cart;
    }

    public function updateShippingMethod(int $shippingMethodId): Cart
    {
        $cart = $this->getCurrentCart();
        $cart->update(['shipping_method_id' => $shippingMethodId]);
        $cart->load(['shipping_method']);
        return $cart;
    }

    public function updatePaymentMethod(int $paymentMethodId): Cart
    {
        $cart = $this->getCurrentCart();
        $cart->update(['payment_method_id' => $paymentMethodId]);
        $cart->load(['payment_method']);
        return $cart;
    }

    /**
     * @throws Throwable
     */
    public function addCartItem(int $productId, int $quantity, PersonalizeInput $personalizeInput): void
    {
        DB::transaction(function () use ($productId, $quantity, $personalizeInput) {
            $cart = $this->getCurrentCart();
            /** @var CartItem $item */
            $item = $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'design_id' => $personalizeInput->designId,
                'color_id' => $personalizeInput->colorId,
                'font_id' => $personalizeInput->fontId,
                'product_size_id' => $personalizeInput->sizeId
            ]);
            $item->text_lines()->createMany($personalizeInput->getTextLinesArray());
            $item->option_items()->sync($personalizeInput->getOptionValueIds());
            $cart->load('items');
        });
    }

    /**
     * @throws Throwable
     */
    public function updateCartItem(int $cartItemId, PersonalizeInput $personalizeInput): void
    {
        DB::transaction(function () use ($cartItemId, $personalizeInput) {
            $cart = $this->getCurrentCart();
            /** @var CartItem $item */
            $item = $cart->items()->findOrFail($cartItemId);
            $item->updateOrFail([
                'design_id' => $personalizeInput->designId,
                'color_id' => $personalizeInput->colorId,
                'font_id' => $personalizeInput->fontId,
                'product_size_id' => $personalizeInput->sizeId
            ]);
            CartItemTextLine::whereCartItemId($cartItemId)->delete();
            $item->text_lines()->createMany($personalizeInput->getTextLinesArray());
            $item->option_items()->sync($personalizeInput->getOptionValueIds());
            $cart->load('items');
        });
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): void
    {
        $this->getCurrentCart()->updateCartItemQuantity($cartItemId, $quantity);
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

    public function canLogin(Cart $cart): ?bool
    {
        if (empty($cart->email)) return null;
        return User::whereEmail($cart->email)->exists();
    }

    public function canApplyCoupon(Cart $cart): bool
    {
        return $cart->coupons->count() === 0;
    }

    public function getUserCart(User $user): ?Cart
    {
        return Cart::withEverything()
            ->where('user_id', '=', $user->id)
            ->first();
    }

    public function createUserCart(User $user): Cart
    {
        return Cart::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function getAnonCart(): ?Cart
    {
        $cartAnonId = session(Cart::SESSION_KEY);
        if (!$cartAnonId) {
            return null;
        }
        return Cart::withEverything()
            ->where('anon_id', '=', $cartAnonId)
            ->first();
    }

    public function createAnonCart(): Cart
    {
        $cartAnonId = session(Cart::SESSION_KEY);
        if (!$cartAnonId) {
            $cartAnonId = uuid_create();
            session([Cart::SESSION_KEY => $cartAnonId]);
        }
        return Cart::factory()->create([
            'anon_id' => $cartAnonId
        ]);
    }

    private function getOrCreateCart(): Cart
    {
        // retrieve or create user cart
        $user = Auth::user();
        if ($user) {
            return $this->getUserCart($user) ?? $this->createUserCart($user);
        }
        // retrieve or create cart for existing anon key
        return $this->getAnonCart() ?? $this->createAnonCart();
    }

    public function mergeCarts(User $user): void
    {
        $anonCart = $this->getAnonCart();
        $anonCartEmpty = $anonCart === null || $anonCart->isEmpty();
        $userCart = $this->getUserCart($user);
        $userCartEmpty = $userCart === null || $userCart->isEmpty();

        $anonCartFresher = $anonCart?->isFresherThan($userCart);
        $shouldDropAnonCart = !$userCartEmpty && ($anonCartEmpty || !$anonCartFresher);

        if ($shouldDropAnonCart) {
            $anonCart?->delete();
        } else {
            $userCart?->delete();
            $this->attachCartToUser($anonCart, $user);
        }
        session()->forget([Cart::SESSION_KEY]);
    }

    public function attachCartToUser(Cart $cart, User $user): void
    {
        $cart->update([
            'user_id' => $user->id,
            'email' => $user->email,
            'anon_id' => null // если не установить null, то будет ошибка при merge
        ]);
    }
}
