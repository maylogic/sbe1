<?php

namespace App\GraphQL\Queries;

use App\Services\CartService;

readonly class GetCartItemsCount
{
    public function __construct(private CartService $cartService)
    {
    }

    public function __invoke(): int
    {
        return $this->cartService->getCurrentCartItemsCount();
    }
}
