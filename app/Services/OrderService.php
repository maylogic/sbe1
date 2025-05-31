<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

readonly class OrderService
{
    public function __construct(private CartService $cartService)
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

    public function placeOrder(?string $comment): Order
    {
        return \DB::transaction(function () use ($comment) {
            $cart = $this->cartService->getCurrentCart();
            $fields = $this->getOrderCreateFieldsFromCart($cart);
            if (is_string($comment)) {
                $comment = substr($comment, 0, 210);
            }
            $fields['comment'] = $comment;
            $order = Order::factory()->create($fields);
            foreach ($cart->items as $item) {
                /** @var OrderItem $orderItem */
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
        return [
            'product_id' => $item->product_id,
            'product_size_id' => $item->product_size_id,
            'quantity' => $item->quantity,
            'design_id' => $item->design_id,
            'color_id' => $item->color_id,
            'font_id' => $item->font_id
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

    public function getOrderCreateFieldsFromCart(Cart $cart): array
    {
        return [
            'email' => $cart->email,
            'user_id' => $cart->user_id,
            'shipping_address_id' => $cart->shipping_address_id,
            'shipping_method_id' => $cart->shipping_method_id,
            'apply_balance' => $cart->apply_balance,
            'anon_id' => $cart->anon_id
        ];
    }
}
