<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesRule>
 */
class SalesRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->unique()->words(3, true),
            'amount' => 10,
            'is_percentage' => true,
            'active' => true,
            'product' => true,
            'shipping' => false,
            'expression' => null,
            'sort' => 0,
            'ends_at' => now()->addMonth(),
        ];
    }
}
