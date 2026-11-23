<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'status' => \App\Enum\OrderStatus::PENDING->value,
            'apply_balance' => false,
            'shipping_method_id' => \App\Models\ShippingMethod::factory(),
            'shipping_address_id' => \App\Models\CustomerAddress::factory(),
            'shipping_price_regular' => 300,
            'shipping_price_discounted' => 300,
            'subtotal' => 0,
            'grand_total_regular' => 300,
            'grand_total_discounted' => 300,
        ];
    }
}
