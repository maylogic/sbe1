<?php

namespace App\GraphQL\Resolvers;

use App\Contracts\IHasPrice;
use App\Data\Price;
use App\Services\PriceService;

readonly class PriceResolver
{
    public function __construct(private PriceService $priceService)
    {

    }

    public function __invoke(
        IHasPrice $rootValue
    ): Price
    {
        return $this->priceService->getPrice($rootValue);
    }
}
