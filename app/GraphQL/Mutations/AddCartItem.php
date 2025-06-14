<?php

namespace App\GraphQL\Mutations;

use App\Data\Input\PersonalizeInput;
use App\Models\Cart;
use App\Services\CartService;

readonly class AddCartItem
{
    public function __construct(private CartService $cartService)
    {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $productId = $args['productId'];
        $quantity = $args['quantity'];
        $personalize = PersonalizeInput::parse($args['personalize']);
        $this->cartService->addCartItem($productId, $quantity, $personalize);
        return $this->cartService->getCurrentCart();
    }
}
