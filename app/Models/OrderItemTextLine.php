<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $order_item_id
 * @property int $product_line_definition_id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductLineDefinition $product_line_definition
 * @method static \Database\Factories\OrderItemTextLineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereProductLineDefinitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItemTextLine whereValue($value)
 * @mixin \Eloquent
 */
class OrderItemTextLine extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemTextLineFactory> */
    use HasFactory;

    protected $fillable = ['product_line_definition_id', 'value'];

    public function product_line_definition(): BelongsTo
    {
        return $this->belongsTo(ProductLineDefinition::class);
    }
}
