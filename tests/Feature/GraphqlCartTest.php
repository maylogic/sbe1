<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductLineDefinition;
use App\Models\ProductOptionItem;
use App\Models\ProductSize;
use App\Models\SalesRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * The storefront talks to the shop only through the GraphQL endpoint,
 * so the cart contract is tested through real HTTP requests, exactly as the
 * Nuxt client does it.
 */
class GraphqlCartTest extends TestCase
{
    use RefreshDatabase;

    private const ADD_TO_CART = <<<'GQL'
    mutation AddToCart($productId: String!, $quantity: Int!, $personalize: PersonalizeInput!) {
        addCartItem(productId: $productId, quantity: $quantity, personalize: $personalize) {
            items {
                id
                product_id
                quantity
                text_lines { value }
                option_items { id }
                price { regular discounted }
            }
        }
    }
    GQL;

    private function graphql(string $query, array $variables = []): TestResponse
    {
        return $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);
    }

    private function product(int $price = 1000): Product
    {
        $product = Product::factory()->pricedAt($price)->create();
        ProductSize::factory()->for($product)->create(['price' => 200]);

        return $product;
    }

    private function personalizeFor(Product $product, ?ProductOptionItem $option = null): array
    {
        $line = ProductLineDefinition::factory()->for($product)->create();

        return [
            'colorId' => null,
            'fontId' => null,
            'designId' => null,
            'sizeId' => (string) $product->sizes()->first()->id,
            'options' => $option
                ? [['optionId' => (string) $option->product_option_id, 'optionValueId' => (string) $option->id]]
                : [],
            'textLines' => [
                ['productLineDefinitionId' => (string) $line->id, 'value' => 'Маша'],
            ],
        ];
    }

    public function test_item_with_personalization_lands_in_the_cart(): void
    {
        $product = $this->product(1000);
        $option = ProductOptionItem::factory()->create(['price' => 250]);

        $response = $this->graphql(self::ADD_TO_CART, [
            'productId' => (string) $product->id,
            'quantity' => 2,
            'personalize' => $this->personalizeFor($product, $option),
        ]);

        $response->assertOk()->assertJsonMissingPath('errors');

        $item = $response->json('data.addCartItem.items.0');
        $this->assertSame($product->id, $item['product_id']);
        $this->assertSame(2, $item['quantity']);
        $this->assertSame('Маша', $item['text_lines'][0]['value']);
        $this->assertSame([(string) $option->id], array_column($item['option_items'], 'id'));

        // 1000 + 200 size + 250 option
        $this->assertSame(1450, $item['price']['regular']);

        $this->assertDatabaseHas('cart_item_text_lines', ['value' => 'Маша']);
    }

    public function test_item_with_unknown_product_is_rejected(): void
    {
        $this->graphql(self::ADD_TO_CART, [
            'productId' => '999999',
            'quantity' => 1,
            'personalize' => [
                'colorId' => null,
                'fontId' => null,
                'designId' => null,
                'sizeId' => null,
                'options' => [],
                'textLines' => [],
            ],
        ])->assertJsonPath('errors.0.extensions.validation.productId.0', 'The selected product id is invalid.');
    }

    public function test_product_price_is_discounted_by_an_active_rule(): void
    {
        $product = $this->product(1000);
        SalesRule::factory()->create(['amount' => 15, 'is_percentage' => true]);

        $query = <<<'GQL'
        query Product($slug: String!) {
            getProductBySlug(slug: $slug) { price { regular is_discounted discounted } }
        }
        GQL;

        $this->graphql($query, ['slug' => $product->slug])
            ->assertOk()
            ->assertJsonPath('data.getProductBySlug.price.regular', 1000)
            ->assertJsonPath('data.getProductBySlug.price.discounted', 850)
            ->assertJsonPath('data.getProductBySlug.price.is_discounted', true);
    }

    public function test_quantity_zero_removes_the_item(): void
    {
        $product = $this->product();
        $this->graphql(self::ADD_TO_CART, [
            'productId' => (string) $product->id,
            'quantity' => 1,
            'personalize' => $this->personalizeFor($product),
        ]);

        $itemId = $this->graphql('query { getCart { items { id } } }')->json('data.getCart.items.0.id');

        $this->graphql(
            'mutation Remove($id: String!, $q: Int!) { updateCartItemQuantity(cartItemId: $id, quantity: $q) { items { id } } }',
            ['id' => (string) $itemId, 'q' => 0]
        )->assertOk();

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_items_count_query_counts_cart_rows(): void
    {
        $product = $this->product();
        $this->graphql(self::ADD_TO_CART, [
            'productId' => (string) $product->id,
            'quantity' => 3,
            'personalize' => $this->personalizeFor($product),
        ]);

        $this->graphql('query { getCartItemsCount }')
            ->assertOk()
            ->assertJsonPath('data.getCartItemsCount', 1);
    }

    public function test_coupon_is_applied_once_and_its_quantity_is_reserved(): void
    {
        $product = $this->product(1000);
        $coupon = Coupon::factory()->create(['discount_percentage' => 10, 'quantity' => 5]);

        $this->graphql(self::ADD_TO_CART, [
            'productId' => (string) $product->id,
            'quantity' => 1,
            'personalize' => $this->personalizeFor($product),
        ]);

        $apply = <<<'GQL'
        mutation Apply($code: String!) {
            applyCoupon(code: $code) {
                coupons { code discount_percentage }
                can_apply_coupon
                totals { grand_total { regular discounted } }
            }
        }
        GQL;

        $this->graphql($apply, ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonMissingPath('errors')
            ->assertJsonPath('data.applyCoupon.coupons.0.code', $coupon->code)
            ->assertJsonPath('data.applyCoupon.can_apply_coupon', false)
            ->assertJsonPath('data.applyCoupon.totals.grand_total.discounted', 1080);

        $this->assertSame(4, $coupon->fresh()->quantity);

        $this->graphql($apply, ['code' => $coupon->code])
            ->assertJsonPath('errors.0.message', 'Купон уже применен');

        $this->assertSame(4, $coupon->fresh()->quantity);
    }

    public function test_unknown_coupon_code_is_rejected(): void
    {
        $this->graphql(
            'mutation Apply($code: String!) { applyCoupon(code: $code) { id } }',
            ['code' => 'XXXX-YYYY']
        )->assertJsonPath('errors.0.message', 'Такого купона нет');
    }

    public function test_removing_a_coupon_returns_its_quantity(): void
    {
        $coupon = Coupon::factory()->create(['discount_percentage' => 10, 'quantity' => 5]);

        $this->graphql('mutation Apply($code: String!) { applyCoupon(code: $code) { id } }', ['code' => $coupon->code]);
        $this->assertSame(4, $coupon->fresh()->quantity);

        $this->graphql('mutation Remove($code: String!) { removeCoupon(code: $code) { coupons { code } } }', ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonMissingPath('errors');

        $this->assertSame(5, $coupon->fresh()->quantity);
    }

    public function test_coupon_with_exhausted_quantity_cannot_be_applied(): void
    {
        $coupon = Coupon::factory()->create(['discount_percentage' => 10, 'quantity' => 0]);

        $this->graphql('mutation Apply($code: String!) { applyCoupon(code: $code) { id } }', ['code' => $coupon->code])
            ->assertJsonPath('errors.0.message', 'Купон закончился');
    }
}
