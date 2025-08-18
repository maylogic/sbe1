<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $cart_item_id
 * @property int $product_line_definition_id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductLineDefinition $product_line_definition
 * @method static \Database\Factories\CartItemTextLineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereProductLineDefinitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItemTextLine whereValue($value)
 * @mixin \Eloquent
 */
class CartItemTextLine extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemTextLineFactory> */
    use HasFactory;

    protected $guarded = [];

    public function product_line_definition(): BelongsTo
    {
        return $this->belongsTo(ProductLineDefinition::class);
    }
}
