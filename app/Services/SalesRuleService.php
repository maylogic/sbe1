<?php

namespace App\Services;

use App\Data\Abstract\PriceContext;
use App\Models\SalesRule;
use App\RuleEvaluator;
use Illuminate\Support\Collection;

class SalesRuleService
{
    /**
     * @var Collection<SalesRule>|null
     */
    protected Collection|null $rules = null;

    public function __construct()
    {
    }

    /**
     * @return Collection<SalesRule>
     */
    public function getActiveRules(): Collection
    {
        if ($this->rules === null) {
            $this->rules = SalesRule::whereActive(1)
                ->where('ends_at', '>', now())
                ->get();
        }
        return $this->rules->toBase();
    }

    /**
     * @return Collection<SalesRule>
     */
    public function getActiveProductRules(): Collection
    {
        return $this->getActiveRules()->filter(fn(SalesRule $rule) => $rule->product);
    }

    /**
     * @return Collection<SalesRule>
     */
    public function getActiveShippingRules(): Collection
    {
        return $this->getActiveRules()->filter(fn(SalesRule $rule) => $rule->shipping);
    }

    public function evaluate(SalesRule $salesRule, ?PriceContext $context = null): bool
    {
        $expression = $salesRule->expression;
        if ($expression === null) {
            return true;
        }
        $evaluator = new RuleEvaluator();
        return $evaluator->evaluate($expression, $context?->toArray() ?? []);
    }

    public function apply(SalesRule $salesRule, int $price): float
    {
        $isPercentage = $salesRule->is_percentage;
        $amount = $salesRule->amount;

        if ($isPercentage) {
            $discount = $price * ($amount / 100);
            $newPrice = $price - round($discount);
        } else {
            $newPrice = $price - $amount;
        }

        return max(0, $newPrice);
    }
}
