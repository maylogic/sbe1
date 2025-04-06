<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class UpdateCartItemQuantity
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
        $quantity = $args['quantity'];
        $this->cartService->updateCartItemQuantity($cartItemId, $quantity);
        return $this->cartService->getCurrentCart();
    }
}
