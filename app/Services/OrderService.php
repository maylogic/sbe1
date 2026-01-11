<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;

readonly class OrderService
{
    public function __construct(private CartService   $cartService,
                                private TotalsService $totalsService,
                                private PriceService  $priceService)
    {
    }

    public function canPlaceOrder(?Cart $cart = null): bool
    {
        $cart = $cart ?? $this->cartService->getCurrentCart();
        return !empty($cart->email)
            && !empty($cart->shipping_address_id)
            && !empty($cart->shipping_method_id);
    }

    public function orderLookup(string $email, string $orderId): ?Order
    {
        return Order::withEverything()
            ->where('email', '=', $email)
            ->where('id', '=', $orderId)->first();
    }

    public function placeOrder(Cart $cart, ?User $user = null, ?string $comment = null): Order
    {
        return \DB::transaction(function () use ($comment, $cart, $user) {
            $fields = $this->getOrderCreateFieldsFromCart($cart);
            if (is_string($comment)) {
                $comment = substr($comment, 0, 210);
            }
            $fields['comment'] = $comment;
            $order = Order::factory()->create($fields);
            foreach ($cart->items as $item) {
                $orderItem = $order->items()->create(
                    $this->getOrderItemFieldsFromCartItem($item)
                );
                $orderItem->text_lines()->createMany(
                    $this->getOrderItemTextLinesFromCartItem($item)
                );
                $orderItem->option_items()->sync(
                    $item->option_items->map(
                        fn($optionItem) => $optionItem->id
                    )
                );
            }
            $order->load('items');
            return $order;
        });
    }

    public function getOrderItemFieldsFromCartItem(CartItem $item): array
    {
        $price = $this->priceService->getPrice($item);
        return [
            'product_id' => $item->product_id,
            'product_size_id' => $item->product_size_id,
            'quantity' => $item->quantity,
            'design_id' => $item->design_id,
            'color_id' => $item->color_id,
            'font_id' => $item->font_id,
            'price_regular' => $price->regular,
            'price_discounted' => $price->discounted,
        ];
    }

    public function getOrderItemTextLinesFromCartItem(CartItem $cartItem): array
    {
        $data = [];

        foreach ($cartItem->text_lines as $textLine) {
            $data[] = [
                'product_line_definition_id' => $textLine->product_line_definition_id,
                'value' => $textLine->value
            ];
        }

        return $data;
    }

    public function getOrderCreateFieldsFromCart(Cart $cart, ?User $user = null): array
    {
        $totals = $this->totalsService->getCartTotals($cart, $user);
        return [
            'email' => $cart->email,
            'user_id' => $cart->user_id,
            'shipping_address_id' => $cart->shipping_address_id,
            'shipping_method_id' => $cart->shipping_method_id,
            'apply_balance' => $cart->apply_balance,
            'anon_id' => $cart->anon_id,
            'shipping_price_regular' => $totals->shipping_price->regular,
            'shipping_price_discounted' => $totals->shipping_price->discounted,
            'subtotal' => $totals->subtotal,
            'grand_total_regular' => $totals->grand_total->regular,
            'grand_total_discounted' => $totals->grand_total->discounted,
        ];
    }
}
