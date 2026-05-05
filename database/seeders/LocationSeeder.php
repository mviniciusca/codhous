<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Laje',
            'Viga / Pilar',
            'Piso Industrial',
            'Fundação / Sapata',
            'Calçada',
            'Escada',
            'Capa de Laje',
            'Enchimento',
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['name' => $location]
            );
        }
    }
}
