<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $base_preview_id
 * @property string|null $text_color
 * @property int $inherit_color
 * @property float $width
 * @property float $height
 * @property float $left
 * @property float $top
 * @property int $first_line
 * @property int $lines
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float $padding_y
 * @property-read \App\Models\Base|null $base
 * @method static \Database\Factories\BasePreviewTextAreaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereBasePreviewId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereFirstLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereInheritColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereLines($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea wherePaddingY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreviewTextArea whereWidth($value)
 * @mixin \Eloquent
 */
class BasePreviewTextArea extends Model
{
    /** @use HasFactory<\Database\Factories\BasePreviewTextAreaFactory> */
    use HasFactory;

    const int EDIT_SCALE = 20; // svg viewport 32, умножаем на 20 и получаем размер редактора 640

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
