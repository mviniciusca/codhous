<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => $this->faker->dateTimeBetween(date('2024-01-01')),
            'code' => $this->faker->numerify('####'),
            'is_active' => $this->faker->boolean(),
            'status' => $this->faker->randomElement([
                'on going',
                'pending',
                'ignored',
                'done'
            ]),
            'content' => [
                'fck' => $this->faker->randomElement([10, 20, 30, 40, 50]),
                'product' => $this->faker->randomElement([1, 2]),
                'area' => $this->faker->randomElement([1, 2, 3, 4, 5]),
                'quantity' => $this->faker->randomElement([1, 3, 5, 7, 10, 15, 30, 60, 90, 100]),
                'postcode' => $this->faker->numerify('2####-###'),
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'customer_phone' => $this->faker->phoneNumber(),
                'street' => $this->faker->streetAddress(),
                'number' => $this->faker->randomNumber(),
                'city' => $this->faker->city(),
                'neighborhood' => $this->faker->city(),
                'state' => $this->faker->countryCode(),
            ]
        ];
    }
}
