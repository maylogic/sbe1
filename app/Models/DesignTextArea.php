<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $design_id
 * @property int $width
 * @property int $height
 * @property int $left
 * @property int $top
 * @property int $first_line
 * @property int $lines
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Design $design
 * @method static \Database\Factories\DesignTextAreaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereFirstLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereLines($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignTextArea whereWidth($value)
 * @mixin \Eloquent
 */
class DesignTextArea extends Model
{
    /** @use HasFactory<\Database\Factories\DesignTextAreaFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }
}
