<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseTextArea extends Model
{
    const int EDIT_SCALE = 20; // svg viewport 32, умножаем на 20 и получаем размер редактора 640

    use HasFactory;

    protected $guarded = [];

    public function base(): BelongsTo
    {
        return $this->belongsTo(Base::class);
    }

    public function svgValues()
    {
        return [
            'width' => $this->width / self::EDIT_SCALE,
            'height' => $this->height / self::EDIT_SCALE,
            'left' => $this->left / self::EDIT_SCALE,
            'top' => $this->top / self::EDIT_SCALE
        ];
    }
}
