<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mail>
 */
class MailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->phoneNumber,
            'message' => $this->faker->paragraph,
            'subject' => $this->faker->sentence,
            'is_read' => $this->faker->boolean(),
            'is_favorite' => $this->faker->boolean(),
            'is_spam' => $this->faker->boolean(),
            'is_sent' => $this->faker->boolean(),
        ];
    }
}
