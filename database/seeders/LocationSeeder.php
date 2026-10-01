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
        // Limpa dados existentes
        Location::query()->delete();

        $locations = [
            'Baldrame',
            'Calçada',
            'Estaca Hélice',
            'Fundação',
            'Guia/Sarjeta',
            'Laje',
            'Piso',
            'Piscina',
            'Radier',
            'Outros',
        ];

        foreach ($locations as $location) {
            Location::create([
                'name' => $location,
                'is_active' => true,
            ]);
        }
    }
}
