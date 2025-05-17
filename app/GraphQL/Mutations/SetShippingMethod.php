<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;

readonly class SetShippingMethod
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
        $id = intval($args['shippingMethodId']);
        return $this->cartService->updateShippingMethod($id);
    }
}
