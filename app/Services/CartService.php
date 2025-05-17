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

    public function updateShippingMethod(int $id): Cart
    {
        $cart = $this->getCurrentCart();
        $cart->update(['shipping_method_id' => $id]);
        $cart->load(['shipping_method']);
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
            ->firstOr(function () use ($user) {
                return Cart::factory()->create([
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            });
    }

    public function getAnonCart(): ?Cart
    {
        $cartAnonId = session(Cart::SESSION_KEY);
        if (!$cartAnonId) {
            $cartAnonId = uuid_create();
            session([Cart::SESSION_KEY => $cartAnonId]);
        }
        return Cart::withEverything()
            ->where('anon_id', '=', $cartAnonId)
            ->firstOr(function () use ($cartAnonId) {
                return Cart::factory()->create([
                    'anon_id' => $cartAnonId
                ]);
            });
    }

    private function getOrCreateCart(): Cart
    {
        // retrieve or create user cart
        $user = Auth::user();
        if ($user) {
            return $this->getUserCart($user);
        }
        // retrieve or create cart for existing anon key
        return $this->getAnonCart();
    }

    public function restoreUserCart(?Cart $oldCart, int $userId): void
    {
        // Восстановить предыдущую корзину пользователя, если анонимная пуста
        // Например, из истории или по умолчанию создать новую
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
