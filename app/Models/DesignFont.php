<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignFont extends Model
{
    /** @use HasFactory<\Database\Factories\DesignFontFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function font(): BelongsTo
    {
        return $this->belongsTo(Font::class);
    }
}
