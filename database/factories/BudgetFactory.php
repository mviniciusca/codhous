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
            'code' => $this->faker->numerify('###'),
            'is_active' => $this->faker->boolean(),
            'status' => $this->faker->randomElement([
                'on going',
                'pending',
                'ignored',
                'done'
            ]),
            'content' => [
                'postcode' => $this->faker->postcode(),
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
