<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Font>
 */
class FontFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->unique()->word(),
            'name' => 'Montserrat',
            'system_name' => 'Montserrat',
            'filename_woff2' => 'montserrat.woff2',
            'filename_ttf' => null,
            'filename_otf' => null,
            'preview_size' => 20,
        ];
    }
}
