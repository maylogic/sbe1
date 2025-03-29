<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

class GetProductBySlug
{
    public function __invoke(
        $rootValue,
        array $args
    ): ?Product
    {
        $slug = $args['slug'];
        return Product::whereSlug($slug)->first();
    }
}
