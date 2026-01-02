<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements IHasPrice
{
    use HasFactory;
    use HasPrice;
    use HasSlug;

    protected $guarded = [];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
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

    public function primary_image(): HasOne
    {
        return $this->hasOne(ProductGalleryItem::class)
            ->where('is_video', '=', '0')
            ->where('primary', '=', '1')
            ->orderBy('sort');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function line_definitions(): HasMany
    {
        return $this->hasMany(ProductLineDefinition::class)
            ->orderBy('sort', 'ASC');
    }
}
