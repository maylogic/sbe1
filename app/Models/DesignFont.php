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
 * @property int $font_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Design $design
 * @property-read \App\Models\Font $font
 * @method static \Database\Factories\DesignFontFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignFont whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DesignFont extends Model
{
    /** @use HasFactory<\Database\Factories\DesignFontFactory> */
    use HasFactory;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function font(): BelongsTo
    {
        return $this->belongsTo(Font::class);
    }
}
