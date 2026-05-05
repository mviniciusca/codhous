<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showcase>
 */
class ShowcaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city . ', ' . $this->faker->stateAbbr,
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl()],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
