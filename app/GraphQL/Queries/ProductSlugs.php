<?php

namespace App\GraphQL\Queries;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductSlugs
{
    public function __invoke(
        $rootValue,
        array $args
    )
    {
        return Product::all(['slug'])->pluck('slug');
    }
}
