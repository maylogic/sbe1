<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductOptionItemFactory> */
    use HasFactory;

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }
}
