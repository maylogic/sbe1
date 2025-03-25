<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $product_option_id
 * @property string $label
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductOption|null $option
 * @method static \Database\Factories\ProductOptionItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem whereProductOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOptionItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductOptionItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductOptionItemFactory> */
    use HasFactory;

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }
}
