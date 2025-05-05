<?php

namespace App\GraphQL\Mutations;

use App\Data\Input\PersonalizeInput;
use App\Models\Cart;
use App\Services\CartService;

readonly class UpdateCartItem
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
        $personalize = PersonalizeInput::parse($args['personalize']);
        $this->cartService->updateCartItem($cartItemId, $personalize);
        return $this->cartService->getCurrentCart();
    }
}
