<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'tracking_code' => $this->faker->uuid,
            'card_number' => $this->faker->creditCardNumber,
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'is_paid' => $this->faker->boolean,

        ];
    }
}
