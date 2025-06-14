<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property int $amount
 * @property int $is_percentage
 * @property int $active
 * @property int $cart
 * @property int $product
 * @property int $shipping
 * @property string|null $expression
 * @property int $sort
 * @property \Illuminate\Support\Carbon $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SalesRuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereCart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereIsPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesRule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SalesRule extends Model
{
    /** @use HasFactory<\Database\Factories\SalesRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'label',
        'expression',
        'amount',
        'is_percentage',
        'active',
        'sort',
        'product',
        'shipping',
        'ends_at'
    ];

    protected $casts = [
        'ends_at' => 'datetime'
    ];
}
