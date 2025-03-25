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
 * @property int $color_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Color $color
 * @property-read \App\Models\Design $design
 * @method static \Database\Factories\DesignColorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignColor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
