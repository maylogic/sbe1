<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignTextArea extends Model
{
    /** @use HasFactory<\Database\Factories\DesignTextAreaFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }
}
