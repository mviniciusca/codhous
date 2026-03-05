<?php

namespace Database\Factories;

use App\Models\ContactAgenda;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactAgendaFactory extends Factory
{
    protected $model = ContactAgenda::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('(21) 9####-####'),
            'company' => fake()->optional(0.6)->company(),
            'position' => fake()->optional(0.4)->jobTitle(),
            'category' => fake()->randomElement(array_keys(ContactAgenda::categoryLabels())),
            'source' => fake()->optional(0.7)->randomElement(array_keys(ContactAgenda::sourceLabels())),
            'notes' => fake()->optional(0.3)->paragraph(),
            'is_favorite' => fake()->boolean(20),
        ];
    }
}
