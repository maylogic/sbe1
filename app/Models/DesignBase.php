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
 * @property int $base_id
 * @property int $left
 * @property int $top
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Base $base
 * @property-read \App\Models\Design $design
 * @method static \Database\Factories\DesignBaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereBaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
