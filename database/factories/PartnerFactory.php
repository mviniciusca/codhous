<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => $this->faker->boolean(),
            'email' => $this->faker->email(),
            'postcode' => $this->faker->postcode(),
            'content' => [
                'address' => [$this->faker->address()],
                'phone' => [$this->faker->phoneNumber()],
                'number' => [$this->faker->randomNumber(3)],
                'city' => [$this->faker->city()],
                'state' => [$this->faker->citySuffix()],
                'country' => [$this->faker->country()],
            ]
        ];
    }
}
