<?php

namespace App\GraphQL\Resolvers;

use App\Data\Totals;
use App\Models\Cart;
use App\Models\Order;
use App\Services\TotalsService;

readonly class TotalsResolver
{
    public function __construct(private TotalsService $totalsService)
    {

    }

    public function __invoke(
        Cart|Order $rootValue
    ): Totals
    {
        if ($rootValue instanceof Cart) {
            return $this->totalsService->getCartTotals($rootValue);
        }
        return $this->totalsService->getOrderTotals($rootValue);
    }
}
