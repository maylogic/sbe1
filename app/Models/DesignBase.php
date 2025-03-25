<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignBase extends Model
{
    /** @use HasFactory<\Database\Factories\DesignBaseFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function base(): BelongsTo
    {
        return $this->belongsTo(Base::class);
    }
}
