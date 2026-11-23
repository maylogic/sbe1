<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductGalleryItem>
 */
class ProductGalleryItemFactory extends Factory
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
            'label' => 'Фото',
            'filename' => 'photo.png',
            'is_video' => false,
            'primary' => false,
            'sort' => 0,
        ];
    }
}
