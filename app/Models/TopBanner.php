<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopBanner extends Model
{
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
