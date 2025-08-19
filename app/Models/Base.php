<?php

namespace App\Models;

use Database\Factories\BaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $label
 * @property string $filename
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BaseTextArea> $text_areas
 * @property-read int|null $text_areas_count
 * @method static \Database\Factories\BaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Base extends Model
{
    /** @use HasFactory<BaseFactory> */
    use HasFactory;

    protected $guarded = [];

    public function text_areas(): HasMany
    {
        return $this->hasMany(BaseTextArea::class);
    }

    public function base_preview(): BelongsTo
    {
        return $this->belongsTo(BasePreview::class);
    }
}
