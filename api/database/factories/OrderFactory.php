<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
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
            'user_id' => User::inRandomOrder()->first()->id,
            'address_snapshot' => [
                'phone' => fake()->phoneNumber(),
                'city' => fake()->city(),
                'street' => fake()->streetAddress(),
                'building' => 'Building ' . fake()->buildingNumber()
            ],
        ];
    }
}
