<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $label
 * @property string $svg
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\BaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereSvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Base whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Base extends Model
{
    /** @use HasFactory<\Database\Factories\BaseFactory> */
    use HasFactory;

    protected $fillable = ['label', 'svg'];
}
