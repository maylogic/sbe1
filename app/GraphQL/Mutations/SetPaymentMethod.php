<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class SetPaymentMethod
{
    public function __construct(
        private CartService $cartService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $id = intval($args['paymentMethodId']);
        return $this->cartService->updatePaymentMethod($id);
    }
}
