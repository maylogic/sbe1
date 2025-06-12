<?php

namespace App\Services;

use App\Enum\SalesRuleType;
use App\Models\SalesRule;
use Illuminate\Database\Eloquent\Collection;

class SalesRuleService
{
    /**
     * @return Collection<SalesRule>
     */
    public function getActiveProductRules(): Collection
    {
        return SalesRule::where('type', '=', SalesRuleType::PRODUCT)->get();
    }
}
