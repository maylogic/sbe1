<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Design> $designs
 * @property-read int|null $designs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductGalleryItem> $gallery
 * @property-read int|null $gallery_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductLineDefinition> $line_definitions
 * @property-read int|null $line_definitions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOption> $options
 * @property-read int|null $options_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSize> $sizes
 * @property-read int|null $sizes_count
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model implements IHasPrice
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;
    use HasPrice;
    use HasSlug;

    protected $fillable = ['name', 'slug', 'description', 'price'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function reverse_side_base(): BelongsTo
    {
        return $this->belongsTo(Base::class);
    }

    public function designs(): BelongsToMany
    {
        return $this->belongsToMany(Design::class, 'product_design')->withTimestamps();
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(ProductGalleryItem::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function line_definitions(): HasMany
    {
        return $this->hasMany(ProductLineDefinition::class);
    }
}
