<?php

namespace Tests\Unit;

use App\Data\Price;
use App\RuleEvaluator;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Discount expressions are written by managers in the admin panel, so a
 * malformed one must never take the shop down: RuleEvaluator swallows the
 * evaluation error and treats the rule as not matching.
 *
 * Expressions are evaluated against typed contexts (App\Data\Abstract\PriceContext),
 * so property access is used, not array access.
 */
class RuleEvaluatorTest extends TestCase
{
    private RuleEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new RuleEvaluator();
    }

    public function test_expression_is_evaluated_against_the_context(): void
    {
        $this->assertTrue($this->evaluator->evaluate(
            'product.price > 500',
            ['product' => (object) ['price' => 1000]]
        ));

        $this->assertFalse($this->evaluator->evaluate(
            'product.price > 500',
            ['product' => (object) ['price' => 100]]
        ));
    }

    public function test_compound_expressions_work_with_price_objects(): void
    {
        $context = ['cart_subtotal' => Price::create(5000)];

        $this->assertTrue($this->evaluator->evaluate(
            'cart_subtotal.regular >= 3000 and cart_subtotal.regular < 10000',
            $context
        ));

        $this->assertFalse($this->evaluator->evaluate('cart_subtotal.regular >= 10000', $context));
        $this->assertTrue($this->evaluator->evaluate('cart_subtotal.is_discounted == false', $context));
    }

    public function test_discounted_price_is_visible_to_the_expression(): void
    {
        $context = ['cart_subtotal' => Price::create(5000, 4000)];

        $this->assertTrue($this->evaluator->evaluate('cart_subtotal.is_discounted == true', $context));
        $this->assertTrue($this->evaluator->evaluate('cart_subtotal.discounted < 5000', $context));
    }

    public function test_broken_expression_does_not_match_instead_of_throwing(): void
    {
        $this->assertFalse($this->evaluator->evaluate(
            'product.price >> ',
            ['product' => (object) ['price' => 1000]]
        ));

        $this->assertFalse($this->evaluator->evaluate(
            'unknown_variable > 1',
            ['product' => (object) ['price' => 1000]]
        ));
    }
}
