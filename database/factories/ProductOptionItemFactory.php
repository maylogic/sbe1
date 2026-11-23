<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOptionItem>
 */
class ProductOptionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_option_id' => \App\Models\ProductOption::factory(),
            'label' => 'Хлопок',
            'price' => 200,
        ];
    }
}
