<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $design_id
 * @property int $left
 * @property int $top
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Design $design
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ProductDesignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductDesign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductDesign extends Model
{
    /** @use HasFactory<\Database\Factories\ProductDesignFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
