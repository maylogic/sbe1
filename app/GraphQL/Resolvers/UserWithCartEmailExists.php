<?php

namespace App\GraphQL\Resolvers;

use App\Models\Cart;
use App\Services\CartService;

readonly class UserWithCartEmailExists
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(Cart $rootValue): ?bool
    {
        return $this->cartService->userWithCartEmailExists($rootValue);
    }
}
