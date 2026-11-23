<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\ProductSize;
use App\Models\SalesRule;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\PriceService;
use App\Services\ShippingService;
use App\Data\ShippingPriceContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prices are data-driven: a sales rule is a row with an amount and an optional
 * Symfony Expression Language expression that is evaluated against a typed context.
 * These tests pin down that behaviour, because a silent regression here means
 * wrong money on every product card and every order.
 */
class PricingTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $price = 1000): Product
    {
        $product = Product::factory()->pricedAt($price)->create();
        ProductSize::factory()->for($product)->create(['price' => 200]);

        return $product;
    }

    private function percentageRule(int $amount, array $attributes = []): SalesRule
    {
        return SalesRule::factory()->create(array_merge([
            'amount' => $amount,
            'is_percentage' => true,
        ], $attributes));
    }

    public function test_product_without_rules_is_not_discounted(): void
    {
        $product = $this->product(1000);

        $price = app(PriceService::class)->getProductPrice($product);

        $this->assertSame(1000, $price->regular);
        $this->assertSame(1000.0, $price->discounted);
        $this->assertFalse($price->is_discounted);
    }

    public function test_percentage_product_rule_discounts_the_price(): void
    {
        $product = $this->product(1000);
        $this->percentageRule(10);

        $price = app(PriceService::class)->getProductPrice($product);

        $this->assertTrue($price->is_discounted);
        $this->assertSame(900.0, $price->discounted);
        $this->assertSame(900, $price->getRegularOrDiscounted());
    }

    public function test_fixed_amount_rule_subtracts_rubles(): void
    {
        $product = $this->product(1000);
        SalesRule::factory()->create([
            'amount' => 300,
            'is_percentage' => false,
        ]);

        $price = app(PriceService::class)->getProductPrice($product);

        $this->assertSame(700.0, $price->discounted);
    }

    public function test_rule_never_produces_a_negative_price(): void
    {
        $product = $this->product(100);
        SalesRule::factory()->create([
            'amount' => 500,
            'is_percentage' => false,
        ]);

        $this->assertSame(0.0, app(PriceService::class)->getProductPrice($product)->discounted);
    }

    public function test_inactive_rule_is_ignored(): void
    {
        $product = $this->product(1000);
        $this->percentageRule(10, ['active' => false]);

        $this->assertFalse(app(PriceService::class)->getProductPrice($product)->is_discounted);
    }

    public function test_expired_rule_is_ignored(): void
    {
        $product = $this->product(1000);
        $this->percentageRule(10, ['ends_at' => now()->subDay()]);

        $this->assertFalse(app(PriceService::class)->getProductPrice($product)->is_discounted);
    }

    public function test_rule_marked_for_shipping_does_not_touch_product_prices(): void
    {
        $product = $this->product(1000);
        $this->percentageRule(10, ['product' => false, 'shipping' => true]);

        $this->assertFalse(app(PriceService::class)->getProductPrice($product)->is_discounted);
    }

    public function test_expression_rule_applies_only_to_matching_products(): void
    {
        $expensive = $this->product(1000);
        $cheap = $this->product(100);

        $this->percentageRule(20, ['expression' => 'product.price > 500']);

        $prices = app(PriceService::class);

        $this->assertSame(800.0, $prices->getProductPrice($expensive)->discounted);
        $this->assertFalse($prices->getProductPrice($cheap)->is_discounted);
    }

    public function test_cart_item_price_sums_product_size_and_options(): void
    {
        $product = $this->product(1000);
        $option = ProductOptionItem::factory()->create(['price' => 250]);

        $item = CartItem::factory()->for($product)->create([
            'quantity' => 2,
            'product_size_id' => $product->sizes()->first()->id,
        ]);
        $item->option_items()->attach($option->id);
        $item->load(['product', 'product_size', 'option_items']);

        $price = app(PriceService::class)->getCartItemPrice($item);

        // 1000 (product) + 200 (size) + 250 (option)
        $this->assertSame(1450, $price->regular);
    }

    public function test_coupon_discount_is_applied_after_sales_rules(): void
    {
        $product = $this->product(1000);
        $this->percentageRule(10);

        $coupon = Coupon::factory()->create(['discount_percentage' => 10]);
        app(CartService::class)->getCurrentCart()->coupons()->attach($coupon->id);

        // 1000 - 10% rule = 900, then -10% coupon = 810
        $this->assertSame(810.0, app(PriceService::class)->getProductPrice($product)->discounted);
    }

    public function test_shipping_rule_discounts_the_shipping_price(): void
    {
        $method = ShippingMethod::factory()->create(['price' => 300]);
        $this->percentageRule(10, ['product' => false, 'shipping' => true]);

        $cart = Cart::factory()->create();
        $context = new ShippingPriceContext($cart, \App\Data\Price::create(1000));

        $price = app(PriceService::class)->getShippingMethodPrice($method, $context);

        $this->assertSame(270.0, $price->discounted);
    }

    public function test_shipping_service_returns_lead_time_per_provider(): void
    {
        $method = ShippingMethod::factory()->create(['price' => 300]);

        $info = app(ShippingService::class)->getShippingInfo($method);

        $this->assertSame(300.0, $info->price);
        $this->assertSame(2, $info->days);
    }
}
