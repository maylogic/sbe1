<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class AddCartItem
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $productId = $args['productId'];
        $quantity = $args['quantity'];
        $this->cartService->addCartItem($productId, $quantity);
        return $this->cartService->getCurrentCart();
    }
}
