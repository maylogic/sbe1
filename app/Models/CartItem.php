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
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $design_id
 * @property int|null $color_id
 * @property int|null $font_id
 * @property int|null $product_size_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Color|null $color
 * @property-read \App\Models\Design|null $design
 * @property-read \App\Models\Font|null $font
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionItem> $option_items
 * @property-read int|null $option_items_count
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSize|null $product_size
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItemTextLine> $text_lines
 * @property-read int|null $text_lines_count
 * @method static \Database\Factories\CartItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CartItem extends Model implements IHasPrice
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'design_id',
        'color_id',
        'font_id',
        'product_size_id'
    ];

    protected $touches = ['cart'];

    /**
     * @return int
     */
    public function getPrice(): int
    {
        $productPrice = $this->product->price;
        $sizePrice = $this->product_size->price;
        $optionsPrices = $this->option_items->reduce(
            fn(int $carry, ProductOptionItem $item) => $carry + $item->price, 0);

        return $productPrice + $sizePrice + $optionsPrices;
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

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
        return $this->hasMany(CartItemTextLine::class)->with(['product_line_definition']);
    }

    public function option_items(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionItem::class, 'cart_item_option_item');
    }

    public function product_size(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }
}
