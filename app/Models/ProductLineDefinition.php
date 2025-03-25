<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string $label
 * @property int $max_length
 * @property int $required
 * @property int $hidden
 * @property string $default_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ProductLineDefinitionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereMaxLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLineDefinition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductLineDefinition extends Model
{
    /** @use HasFactory<\Database\Factories\ProductLineDefinitionFactory> */
    use HasFactory;

    protected $fillable = ['label', 'max_length', 'required', 'hidden', 'default_value'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
