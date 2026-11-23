<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An anonymous visitor fills a cart, then logs in. Two carts collapse into one:
 * identical personalizations are summed, distinct ones are moved, coupons are
 * carried over and the anonymous cart is deleted. Getting this wrong duplicates
 * or loses orders.
 */
class CartMergeTest extends TestCase
{
    use RefreshDatabase;

    private function itemFor(Cart $cart, Product $product, int $quantity): CartItem
    {
        return $cart->items()->create([
            'product_id' => $product->id,
            'product_size_id' => $product->sizes()->first()?->id,
            'quantity' => $quantity,
        ]);
    }

    public function test_identical_items_are_summed_and_distinct_ones_moved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        ProductSize::factory()->for($product)->create();
        $otherProduct = Product::factory()->create();
        ProductSize::factory()->for($otherProduct)->create();

        session([Cart::SESSION_KEY => 'anon-uuid']);

        $anonCart = Cart::factory()->create(['anon_id' => 'anon-uuid']);
        $userCart = Cart::factory()->create(['user_id' => $user->id, 'email' => $user->email]);

        $anonSize = $product->sizes()->first();
        $identicalInAnon = $this->itemFor($anonCart, $product, 2);
        $identicalInAnon->update(['product_size_id' => $anonSize->id]);

        $identicalInUser = $this->itemFor($userCart, $product, 1);
        $identicalInUser->update(['product_size_id' => $anonSize->id]);

        $onlyInAnon = $this->itemFor($anonCart, $otherProduct, 3);

        app(CartService::class)->mergeCarts($user);

        $this->assertDatabaseMissing('carts', ['id' => $anonCart->id]);

        $userCart->refresh()->load('items');
        $this->assertCount(2, $userCart->items);
        $this->assertSame(3, $userCart->items->firstWhere('product_id', $product->id)->quantity);
        $this->assertNotNull($userCart->items->firstWhere('id', $onlyInAnon->id));
    }

    public function test_coupon_from_anonymous_cart_is_carried_over(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['quantity' => 5]);
        $product = Product::factory()->create();

        session([Cart::SESSION_KEY => 'anon-uuid']);

        $anonCart = Cart::factory()->create(['anon_id' => 'anon-uuid']);
        $userCart = Cart::factory()->create(['user_id' => $user->id, 'email' => $user->email]);

        $anonCart->coupons()->attach($coupon->id);
        $this->itemFor($anonCart, $product, 1);

        app(CartService::class)->mergeCarts($user);

        $this->assertTrue($userCart->fresh()->isCouponApplied($coupon));
    }

    public function test_anonymous_cart_is_attached_when_user_has_none(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        session([Cart::SESSION_KEY => 'anon-uuid']);
        $anonCart = Cart::factory()->create(['anon_id' => 'anon-uuid']);
        $this->itemFor($anonCart, $product, 1);

        app(CartService::class)->mergeCarts($user);

        $anonCart->refresh();
        $this->assertSame($user->id, $anonCart->user_id);
        $this->assertNull($anonCart->anon_id);
        $this->assertSame($user->email, $anonCart->email);
    }
}
