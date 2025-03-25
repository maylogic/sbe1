<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public function designs(): HasMany
    {
        return $this->hasMany(ProductDesign::class);
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

    public function lineDefinitions(): HasMany
    {
        return $this->hasMany(ProductLineDefinition::class);
    }
}
