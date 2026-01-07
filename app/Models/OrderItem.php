<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model implements IHasPrice
{
    use HasFactory;
    use HasPrice;

    protected $guarded = [];

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
