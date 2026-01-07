<?php

namespace App\Data;

use App\Data\Abstract\PriceContext;
use App\Models\Product;

readonly class ProductPriceContext extends PriceContext
{
    public function __construct(public Product $product)
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        return ['product' => $this->product];
    }

    public function key(): string
    {
        return "PriceContext::Product::{$this->product->id}";
    }
}
