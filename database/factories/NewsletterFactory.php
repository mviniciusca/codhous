<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Newsletter>
 */
class NewsletterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->email(),
            'name' => $this->faker->name(),
            'is_active' => $this->faker->boolean(),
            'created_at' => $this->faker->dateTimeBetween(date('2024-01-01'))
        ];
    }
}
