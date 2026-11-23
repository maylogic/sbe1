<?php

namespace Tests\Feature;

use App\Data\Input\OptionInput;
use App\Data\Input\PersonalizeInput;
use App\Data\Input\TextLineInput;
use App\Enum\OrderStatus;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductLineDefinition;
use App\Models\ProductOptionItem;
use App\Models\ProductSize;
use App\Models\SalesRule;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Placing an order is the one place where the cart stops being a draft:
 * items, personalization, prices and totals are frozen into the order inside
 * a single transaction. A regression here loses money silently, so the whole
 * cart-to-order copy is asserted field by field.
 */
class PlaceOrderTest extends TestCase
{
    use RefreshDatabase;

    private function fillCart(int $quantity = 2, int $productPrice = 1000): array
    {
        $product = Product::factory()->pricedAt($productPrice)->create();
        $size = ProductSize::factory()->for($product)->create(['price' => 200]);
        $option = ProductOptionItem::factory()->create(['price' => 250]);
        $line = ProductLineDefinition::factory()->for($product)->create();

        $personalize = new PersonalizeInput(
            colorId: null,
            fontId: null,
            designId: null,
            sizeId: (string) $size->id,
            options: [new OptionInput((string) $option->product_option_id, (string) $option->id)],
            textLines: [new TextLineInput((string) $line->id, 'Маша')],
        );

        $cartService = app(CartService::class);
        $cartService->addCartItem($product->id, $quantity, $personalize);

        $address = CustomerAddress::factory()->create();
        $method = ShippingMethod::factory()->create(['price' => 300]);

        $cart = $cartService->getCurrentCart();
        $cart->update([
            'email' => 'buyer@example.com',
            'shipping_address_id' => $address->id,
            'shipping_method_id' => $method->id,
        ]);

        return [$cart->fresh(), $product, $option, $line, $method];
    }

    public function test_cart_is_not_placeable_without_email_address_and_shipping_method(): void
    {
        $product = Product::factory()->create();
        app(CartService::class)->getCurrentCart()->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertFalse(app(OrderService::class)->canPlaceOrder());
    }

    public function test_order_copies_items_personalization_and_totals(): void
    {
        [$cart, $product, $option, $line, $method] = $this->fillCart(quantity: 2);

        $this->assertTrue(app(OrderService::class)->canPlaceOrder($cart));

        $order = app(OrderService::class)->placeOrder($cart, null, 'Позвонить заранее');

        $this->assertSame(OrderStatus::PENDING, $order->status);
        $this->assertSame('buyer@example.com', $order->email);
        $this->assertSame('Позвонить заранее', $order->comment);
        $this->assertSame($method->id, $order->shipping_method_id);

        $order->load('items.text_lines', 'items.option_items');
        $this->assertCount(1, $order->items);

        $item = $order->items->first();
        $this->assertSame($product->id, $item->product_id);
        $this->assertSame(2, $item->quantity);

        // 1000 + 200 size + 250 option
        $this->assertSame(1450, $item->price_regular);
        $this->assertSame(1450.0, (float) $item->price_discounted);

        $this->assertSame('Маша', $item->text_lines->first()->value);
        $this->assertSame($line->id, $item->text_lines->first()->product_line_definition_id);
        $this->assertSame([$option->id], $item->option_items->pluck('id')->all());

        // subtotal = 2 * 1450, shipping 300
        $this->assertSame(2900, $order->subtotal);
        $this->assertSame(300, $order->shipping_price_regular);
        $this->assertSame(3200, $order->grand_total_regular);
    }

    public function test_sales_rule_is_burned_into_order_prices(): void
    {
        SalesRule::factory()->create(['amount' => 10, 'is_percentage' => true]);

        [$cart] = $this->fillCart(quantity: 1);

        $order = app(OrderService::class)->placeOrder($cart);

        $item = $order->items()->first();
        $this->assertSame(1450, $item->price_regular);
        $this->assertSame(1305.0, (float) $item->price_discounted);
        // 1450 - 10% = 1305 for the item, plus 300 shipping that no rule touches
        $this->assertSame(1605.0, (float) $order->grand_total_discounted);
    }

    public function test_order_lookup_requires_both_email_and_order_id(): void
    {
        [$cart] = $this->fillCart();
        $order = app(OrderService::class)->placeOrder($cart);

        $this->assertNotNull(app(OrderService::class)->orderLookup('buyer@example.com', (string) $order->id));
        $this->assertNull(app(OrderService::class)->orderLookup('other@example.com', (string) $order->id));
        $this->assertNull(app(OrderService::class)->orderLookup('buyer@example.com', '999999'));
    }

    public function test_full_purchase_flow_through_graphql(): void
    {
        $product = Product::factory()->pricedAt(1000)->create();
        $size = ProductSize::factory()->for($product)->create(['price' => 200]);
        $line = ProductLineDefinition::factory()->for($product)->create();
        $method = ShippingMethod::factory()->create(['price' => 300]);

        $call = function (string $query, array $variables = []) {
            return $this->postJson('/graphql', ['query' => $query, 'variables' => $variables]);
        };

        $call(
            'mutation Add($productId: String!, $quantity: Int!, $personalize: PersonalizeInput!) {
                addCartItem(productId: $productId, quantity: $quantity, personalize: $personalize) { id }
            }',
            [
                'productId' => (string) $product->id,
                'quantity' => 2,
                'personalize' => [
                    'colorId' => null,
                    'fontId' => null,
                    'designId' => null,
                    'sizeId' => (string) $size->id,
                    'options' => [],
                    'textLines' => [['productLineDefinitionId' => (string) $line->id, 'value' => 'Маша']],
                ],
            ]
        )->assertJsonMissingPath('errors');

        $call('mutation SetEmail($email: String!) { setCartEmail(email: $email) { id } }', ['email' => 'buyer@example.com'])
            ->assertJsonMissingPath('errors');

        $addressId = $call(
            'mutation Address { createShippingAddress(address1: "ул. Ленина, 1", city: "Новосибирск", country: "RU", first_name: "Иван", last_name: "Петров", phone_number: "+7 900 000-00-00", postal_code: "630000") { id } }'
        )->json('data.createShippingAddress.id');

        $call(
            'mutation SetMethod($id: String!) { setShippingMethod(shippingMethodId: $id) { can_place_order } }',
            ['id' => (string) $method->id]
        )->assertJsonPath('data.setShippingMethod.can_place_order', true);

        $call(
            'mutation Place($comment: String, $subscribe: Boolean!) {
                placeOrder(comment: $comment, subscribe: $subscribe) {
                    id
                    totals { subtotal grand_total { regular discounted } }
                    items { quantity product { sku } text_lines { value } }
                }
            }',
            ['comment' => null, 'subscribe' => false]
        )
            ->assertOk()
            ->assertJsonMissingPath('errors')
            ->assertJsonPath('data.placeOrder.items.0.product.sku', $product->sku)
            ->assertJsonPath('data.placeOrder.items.0.quantity', 2)
            ->assertJsonPath('data.placeOrder.items.0.text_lines.0.value', 'Маша')
            ->assertJsonPath('data.placeOrder.totals.subtotal', 2400)
            ->assertJsonPath('data.placeOrder.totals.grand_total.regular', 2700);

        $this->assertDatabaseHas('orders', [
            'email' => 'buyer@example.com',
            'status' => OrderStatus::PENDING->value,
            'grand_total_regular' => 2700,
        ]);
    }

    public function test_placing_an_order_without_email_fails(): void
    {
        $this->postJson('/graphql', [
            'query' => 'mutation { placeOrder(comment: null, subscribe: false) { id } }',
        ])->assertJsonPath('errors.0.message', 'Cannot place order');

        $this->assertDatabaseCount('orders', 0);
    }
}
