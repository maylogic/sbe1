<?php

namespace App\GraphQL\Resolvers;

use App\Contracts\IHasShipping;
use App\Models\ShippingMethod;
use App\Services\PriceService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

readonly class EstimateDeliveryTimeAndPrice
{
    public function __construct(private PriceService $priceService)
    {
    }

    public function __invoke(
        IHasShipping   $rootValue,
        array          $args,
        GraphQLContext $context,
        ResolveInfo    $resolveInfo
    ): ?ShippingMethod
    {
        $shippingMethod = $rootValue->getShippingMethod();
        $address = $rootValue->getShippingAddress();
        if (!$shippingMethod) {
            return null;
        }
        if (!$address) {
            return $shippingMethod;
        }
        // estimate delivery time and price based on shipping method API type and customer address
        $estimatedDelivery = '1 день';
        $shippingMethod->price = $this->priceService->getCartShippingPrice($rootValue);
        $shippingMethod->estimated_delivery = $estimatedDelivery;
        return $shippingMethod;
    }
}
