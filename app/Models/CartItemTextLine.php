<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItemTextLine extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemTextLineFactory> */
    use HasFactory;

    public function definition(): BelongsTo
    {
        return $this->belongsTo(ProductLineDefinition::class);
    }
}
