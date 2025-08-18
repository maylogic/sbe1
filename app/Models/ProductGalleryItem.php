<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property int $product_id
 * @property string $filename
 * @property int $is_video
 * @property int $primary
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ProductGalleryItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereIsVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductGalleryItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductGalleryItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductGalleryItemFactory> */
    use HasFactory;

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
