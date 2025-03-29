<?php

namespace App\GraphQL\Queries;

use App\Models\Cart;
use App\Services\CartService;

readonly class GetCart
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(): Cart
    {
        return $this->cartService->getCurrentCart();
    }
}
