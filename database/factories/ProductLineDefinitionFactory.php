<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductLineDefinition>
 */
class ProductLineDefinitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'label' => 'Имя ребёнка',
            'max_length' => 20,
            'required' => true,
            'hidden' => false,
            'default_value' => null,
            'sort' => 0,
        ];
    }
}
