<?php

namespace App\Services;

use App\Data\Input\PersonalizeInput;
use App\Models\Cart;
use App\Models\CartItemTextLine;
use App\Models\Coupon;
use App\Models\User;
use App\Traits\MemoizeMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CartService
{
    use MemoizeMethods;

    public function getCurrentCart(): Cart
    {
        $userId = Auth::user()?->id;
        $cartAnonId = session(Cart::SESSION_KEY);
        $cacheKey = $userId ? 'uid-' . $userId : 'anon-' . $cartAnonId;

        return $this->memoize($cacheKey, fn() => $this->getOrCreateCart());
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
        return $cart;
    }

    public function updateShippingMethod(int $shippingMethodId): Cart
    {
        $cart = $this->getCurrentCart();
        $cart->update(['shipping_method_id' => $shippingMethodId]);
        return $cart;
    }

    /**
     * @throws Throwable
     */
    public function addCartItem(int $productId, int $quantity, PersonalizeInput $personalizeInput): void
    {
        DB::transaction(function () use ($productId, $quantity, $personalizeInput) {
            $cart = $this->getCurrentCart();
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
        $this->getCurrentCart()->applyCouponIfNotApplied($coupon);
    }

    public function removeCoupon(Coupon $coupon): void
    {
        $this->getCurrentCart()->removeCouponIfApplied($coupon);
    }

    public function userWithCartEmailExists(Cart $cart): ?bool
    {
        if (empty($cart->email)) {
            return false;
        }
        return User::whereEmail($cart->email)->exists();
    }

    public function canApplyCoupon(Cart $cart): bool
    {
        return $cart->coupons->count() === 0;
    }

    public function getUserCart(User $user): ?Cart
    {
        return Cart::where('user_id', '=', $user->id)->first();
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
        return Cart::where('anon_id', '=', $cartAnonId)->first();
    }

    public function createAnonCart(): Cart
    {
        $cartAnonId = session(Cart::SESSION_KEY);
        if (!$cartAnonId) {
            $cartAnonId = Str::uuid()->toString();
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
        DB::transaction(function () use ($user) {
            $anonCart = Cart::forMerging()
                ->where('anon_id', session(Cart::SESSION_KEY))
                ->first();
            $userCart = Cart::forMerging()
                ->where('user_id', $user->id)
                ->first();

            // Если нет анонимной корзины — ничего не делаем
            if (!$anonCart) {
                return;
            }

            // Если нет пользовательской корзины — просто привязываем анонимную
            if (!$userCart) {
                $this->attachCartToUser($anonCart, $user);
                return;
            }

            // Обе корзины существуют — переносим товары из anonCart в userCart
            foreach ($anonCart->items as $item) {
                $existingItem = $userCart->items->first(fn($userItem) => $userItem->isSameAs($item));

                if ($existingItem) {
                    $existingItem->update(['quantity' => $existingItem->quantity + $item->quantity]);
                } else {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }

            // Переносим купон (если в userCart его нет)
            foreach ($anonCart->coupons as $coupon) {
                $userCart->applyCouponIfNotApplied($coupon);
            }

            // Сохраняем адрес/метод доставки из более свежей корзины (опционально)
            if ($anonCart->isFresherThan($userCart)) {
                $upd = [];
                if ($anonCart->shipping_address_id) {
                    $upd['shipping_address_id'] = $anonCart->shipping_address_id;
                }
                if ($anonCart->shipping_method_id) {
                    $upd['shipping_method_id'] = $anonCart->shipping_method_id;
                }
                $userCart->update($upd);
            }

            // Удаляем анонимную корзину (теперь она пуста или не нужна)
            $anonCart->delete();
        });

        session()->forget(Cart::SESSION_KEY);
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
