<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address1' => 'ул. Ленина, 1',
            'address2' => null,
            'city' => 'Новосибирск',
            'country' => 'RU',
            'first_name' => 'Иван',
            'last_name' => 'Петров',
            'phone_number' => '+7 900 000-00-00',
            'postal_code' => '630000',
            'state' => 'Новосибирская область',
        ];
    }
}
