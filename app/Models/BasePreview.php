<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BasePreview extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function text_areas(): HasMany
    {
        return $this->hasMany(BasePreviewTextArea::class);
    }
}
