<?php

namespace App\GraphQL\Resolvers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\BalanceService;

readonly class CanApplyBalance
{
    public function __construct(private BalanceService $balanceService)
    {
    }

    public function __invoke(Cart|Order $rootValue): bool
    {
        return $this->balanceService->canApplyBalance($rootValue);
    }
}
