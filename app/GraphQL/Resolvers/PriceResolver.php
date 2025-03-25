<?php

namespace App\GraphQL\Resolvers;

use App\Contracts\IHasPrice;
use App\Data\Price;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PriceResolver
{
    public function __invoke(IHasPrice $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Price
    {
        // применить тут различные скидки, в том числе купон
        return new Price($rootValue->getPrice(), false, $rootValue->getPrice());
    }
}
