<?php

namespace App\Models;

use App\Contracts\IHasPrice;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model implements IHasPrice
{
    use HasPrice;
    use HasFactory;

    protected $guarded = [];

    protected $touches = ['cart'];

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

    public function scopeWithPersonalization($query)
    {
        return $query->with(['option_items', 'text_lines']);
    }

    public function isSameAs(self $other): bool
    {
        // Сравниваем основные атрибуты
        if (
            $this->product_id !== $other->product_id ||
            $this->design_id !== $other->design_id ||
            $this->color_id !== $other->color_id ||
            $this->font_id !== $other->font_id ||
            $this->product_size_id !== $other->product_size_id
        ) {
            return false;
        }

        // Сравниваем option_items (набор ID)
        $thisOptionIds = $this->option_items->pluck('id')->sort()->values();
        $otherOptionIds = $other->option_items->pluck('id')->sort()->values();

        if ($thisOptionIds->toArray() !== $otherOptionIds->toArray()) {
            return false;
        }

        // Сравниваем text_lines: должны совпадать все пары (definition_id => value)
        $thisTextLines = $this->text_lines->mapWithKeys(fn ($line) => [$line->product_line_definition_id => $line->value])->sortKeys();
        $otherTextLines = $other->text_lines->mapWithKeys(fn ($line) => [$line->product_line_definition_id => $line->value])->sortKeys();

        return $thisTextLines->toArray() === $otherTextLines->toArray();
    }
}
