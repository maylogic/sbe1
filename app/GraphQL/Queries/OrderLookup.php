<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Services\OrderService;

readonly class OrderLookup
{
    public function __construct(
        private OrderService $orderService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): ?Order
    {
        $email = $args['email'];
        $orderId = $args['orderId'];
        return $this->orderService->orderLookup($email, $orderId);
    }
}
