<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionItem extends Model implements IHasPrice
{
    use HasFactory;
    use HasPrice;

    protected $guarded = [];

    public function product_option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }
}
