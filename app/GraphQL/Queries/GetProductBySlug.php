<?php

namespace App\GraphQL\Queries;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetProductBySlug
{
    public function __invoke(
        $rootValue,
        array $args
    ): Product
    {
        $slug = $args['slug'];
        try {
            return Product::whereSlug($slug)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Продукт не найден');
        }
    }
}
