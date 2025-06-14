<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $price_regular
 * @property int $price_discounted
 * @property int|null $design_id
 * @property int|null $color_id
 * @property int|null $font_id
 * @property int|null $product_size_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Color|null $color
 * @property-read \App\Models\Design|null $design
 * @property-read \App\Models\Font|null $font
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionItem> $option_items
 * @property-read int|null $option_items_count
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSize|null $product_size
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItemTextLine> $text_lines
 * @property-read int|null $text_lines_count
 * @method static \Database\Factories\OrderItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePriceDiscounted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePriceRegular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderItem extends Model implements IHasPrice
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'design_id',
        'color_id',
        'font_id',
        'product_size_id',
        'price_regular',
        'price_discounted',
    ];

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function font(): BelongsTo
    {
        return $this->belongsTo(Font::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function text_lines(): HasMany
    {
        return $this->hasMany(OrderItemTextLine::class)->with(['product_line_definition']);
    }

    public function option_items(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionItem::class, 'order_item_option_item');
    }

    public function product_size(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }
}
