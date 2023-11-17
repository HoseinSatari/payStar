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
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'order_code' => $this->faker->unique()->uuid,
            'status' => $this->faker->randomElement(['unpaid', 'paid', 'preparation', 'done', 'cancel', 'returned']),
            'cart_number' => $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
