<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 *
 *
 * @property int $id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Color> $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Font> $fonts
 * @property-read int|null $fonts_count
 * @method static \Database\Factories\DesignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Design whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Design extends Model
{
    /** @use HasFactory<\Database\Factories\DesignFactory> */
    use HasFactory;

    protected $fillable = ['label'];

    public function fonts(): BelongsToMany
    {
        return $this->belongsToMany(Font::class, 'design_font');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'design_color');
    }

    public function bases(): BelongsToMany
    {
        return $this->belongsToMany(Base::class, 'design_base');
    }
}
