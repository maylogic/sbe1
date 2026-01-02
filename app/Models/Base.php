<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Base extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function text_areas(): HasMany
    {
        return $this->hasMany(BaseTextArea::class);
    }

    public function base_preview(): BelongsTo
    {
        return $this->belongsTo(BasePreview::class);
    }
}
