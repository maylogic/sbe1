<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemTextLine extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product_line_definition(): BelongsTo
    {
        return $this->belongsTo(ProductLineDefinition::class);
    }
}
