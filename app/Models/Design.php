<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Design extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function fonts(): BelongsToMany
    {
        return $this->belongsToMany(Font::class, 'design_font');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'design_color');
    }

    public function bases(): BelongsToMany
    {
        return $this->belongsToMany(Base::class, 'design_base');
    }
}
