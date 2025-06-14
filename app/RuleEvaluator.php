<?php

namespace App;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RuleEvaluator
{
    protected ExpressionLanguage $evaluator;

    public function __construct()
    {
        $this->evaluator = new ExpressionLanguage();
    }

    public function evaluate(string $expression, array $context): bool
    {
        try {
            return $this->evaluator->evaluate($expression, $context);
        } catch (\Exception $e) {
            return false;
        }
    }
}
