<?php

namespace App\Models;

use Database\Factories\BaseTextAreaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseTextArea extends Model
{
    /** @use HasFactory<BaseTextAreaFactory> */
    use HasFactory;

    protected $fillable = ['width', 'height', 'left', 'top', 'first_line', 'lines'];

    public function base(): BelongsTo
    {
        return $this->belongsTo(Base::class);
    }
}
