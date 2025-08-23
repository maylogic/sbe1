<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property string $filename
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BasePreviewTextArea> $text_areas
 * @property-read int|null $text_areas_count
 * @method static \Database\Factories\BasePreviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasePreview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BasePreview extends Model
{
    /** @use HasFactory<\Database\Factories\BasePreviewFactory> */
    use HasFactory;

    protected $guarded = [];

    public function text_areas(): HasMany
    {
        return $this->hasMany(BasePreviewTextArea::class);
    }
}
