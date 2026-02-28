<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Laje',
                'Calçada',
                'Piso Garagem',
                'Piso Industrial',
                'Fundação / Sapata',
                'Viga Baldrame',
                'Pilar / Coluna',
                'Rampa de Acesso',
                'Estacionamento',
                'Quadra Poliesportiva',
            ])
        ];
    }

    /**
     * State for specific construction locations
     */
    public function construction(): self
    {
        return $this->sequence(
            ['name' => 'Laje'],
            ['name' => 'Calçada'],
            ['name' => 'Piso Garagem'],
            ['name' => 'Piso Industrial'],
            ['name' => 'Fundação'],
            ['name' => 'Pilar'],
            ['name' => 'Piscina'],
        );
    }
}
