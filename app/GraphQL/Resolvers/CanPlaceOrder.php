<?php

namespace App\GraphQL\Resolvers;

use App\Models\Cart;
use App\Services\OrderService;

readonly class CanPlaceOrder
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function __invoke(Cart $rootValue): bool
    {
        return $this->orderService->canPlaceOrder($rootValue);
    }
}
