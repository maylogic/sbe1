<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class ToggleApplyBalance
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $cart = $this->cartService->getCurrentCart();
        if ($cart->can_apply_balance()) {

        }

        return $cart;
    }
}
