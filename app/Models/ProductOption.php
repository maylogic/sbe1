<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ProductOptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductOption extends Model
{
    /** @use HasFactory<\Database\Factories\ProductOptionFactory> */
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductOptionItem::class);
    }
}
