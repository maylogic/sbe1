<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $sales_rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SalesRule $sales_rule
 * @method static \Database\Factories\TopBannerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner hasActiveSalesRule()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner whereSalesRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopBanner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TopBanner extends Model
{
    /** @use HasFactory<\Database\Factories\TopBannerFactory> */
    use HasFactory;

    protected $guarded = [];

    public function sales_rule(): BelongsTo
    {
        return $this->belongsTo(SalesRule::class);
    }

    public function scopeHasActiveSalesRule($query)
    {
        return $query->whereHas('sales_rule', function ($q) {
            $q->where('active', 1)
                ->where('ends_at', '>', now());
        });
    }
}
