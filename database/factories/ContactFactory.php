<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3677.6792433168043!2d-43.243925399999995!3d-22.814346300000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x99798ef85b9049%3A0x1aa0a59f717077c8!2sAv.%20Vinte%20de%20Janeiro%20-%20Gale%C3%A3o%2C%20Rio%20de%20Janeiro%20-%20RJ%2C%20Brasil!5e0!3m2!1spt-BR!2sus!4v1717079962792!5m2!1spt-BR!2sus',
            'address' => 'Av. Vinte de Janeiro, S/N, Rio de Janeiro - RJ',
        ];
    }
}
