<?php

namespace App\GraphQL\Resolvers;

use App\Data\Totals;
use App\Models\Cart;
use App\Services\TotalsService;

readonly class TotalsResolver
{
    public function __construct(private TotalsService $totalsService)
    {

    }

    public function __invoke(
        Cart $rootValue
    ): Totals
    {
        return $this->totalsService->getCartTotals($rootValue);
    }
}
