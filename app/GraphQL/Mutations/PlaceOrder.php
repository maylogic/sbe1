<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;

readonly class PlaceOrder
{
    public function __construct(
        private OrderService $orderService,
        private CartService  $cartService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Order
    {
        $comment = $args['comment'];
        $subscribe = $args['subscribe'];
        if (!$this->orderService->canPlaceOrder()) {
            throw new \InvalidArgumentException('Cannot place order');
        }
        $cart = $this->cartService->getCurrentCart();
        $user = \Auth::user();
        return $this->orderService->placeOrder($cart, $user, $comment);
    }
}
