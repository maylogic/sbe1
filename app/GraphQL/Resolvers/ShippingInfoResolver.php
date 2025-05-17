<?php

namespace App\GraphQL\Resolvers;

use App\Data\ShippingInfo;
use App\Models\ShippingMethod;
use App\Services\ShippingService;

readonly class ShippingInfoResolver
{
    public function __construct(private ShippingService $shippingService)
    {

    }

    public function __invoke(
        ShippingMethod $rootValue
    ): ShippingInfo
    {
        return $this->shippingService->getShippingInfo($rootValue);
    }
}
