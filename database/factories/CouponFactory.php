<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => CouponService::generateUniqueCode(),
            'discount_percentage' => 10,
            'quantity' => null,
            'ends_at' => now()->addMonth(),
        ];
    }
}
