<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignColor extends Model
{
    /** @use HasFactory<\Database\Factories\DesignColorFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }
}
