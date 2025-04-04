<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class RemoveCartItem
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $cartItemId = $args['cartItemId'];
        $this->cartService->removeCartItemId($cartItemId);
        return $this->cartService->getCurrentCart();
    }
}
