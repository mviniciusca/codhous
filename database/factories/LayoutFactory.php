<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Layout>
 */
class LayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => [
                'status' => true,
                'bg_position' => 'bg-top',
                'bg_repeat' => 'bg-no-repeat',
                'bg_attachment' => 'bg-scroll',
                'bg_size' => 'bg-cover',
                'bg_height' => 680,
            ],
            'maintenance' => [
                'show' => true,
                'title' => 'Maintenance Mode',
                'message' => 'This application is under maintenance. Please, come back soon. Thanks!',
            ],
        ];
    }
}
