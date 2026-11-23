<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####')),
            'description' => fake()->paragraph(),
            'price' => 1000,
            'meta_title' => fake()->sentence(4),
            'meta_description' => fake()->sentence(8),
        ];
    }

    public function pricedAt(int $price): static
    {
        return $this->state(fn () => ['price' => $price]);
    }
}
