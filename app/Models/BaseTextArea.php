<?php

namespace App\Models;

use Database\Factories\BaseTextAreaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $base_id
 * @property float $width
 * @property float $height
 * @property float $left
 * @property float $top
 * @property int $first_line
 * @property int $lines
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Base $base
 * @method static \Database\Factories\BaseTextAreaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereBaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereFirstLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereLines($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseTextArea whereWidth($value)
 * @mixin \Eloquent
 */
class BaseTextArea extends Model
{
    const EDIT_SCALE = 20; // svg viewport 32, умножаем на 20 и получаем размер редактора 640

    /** @use HasFactory<BaseTextAreaFactory> */
    use HasFactory;

    protected $fillable = ['width', 'height', 'left', 'top', 'first_line', 'lines'];

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
