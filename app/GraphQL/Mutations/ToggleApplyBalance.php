<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\BalanceService;
use App\Services\CartService;

readonly class ToggleApplyBalance
{
    public function __construct(private CartService $cartService, private BalanceService $balanceService)
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $cart = $this->cartService->getCurrentCart();
        if ($this->balanceService->canApplyBalance($cart, \Auth::user())) {
            $cart->update(['apply_balance' => !$cart->apply_balance]);
        }

        return $cart;
    }
}
