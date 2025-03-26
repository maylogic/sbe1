<?php

namespace App\Models;

use Database\Factories\BaseFactory;
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
 * @method static BaseFactory factory($count = null, $state = [])
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

    protected $fillable = ['label', 'filename'];

    public function text_areas(): HasMany
    {
        return $this->hasMany(BaseTextArea::class);
    }
}
