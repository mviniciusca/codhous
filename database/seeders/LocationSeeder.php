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
            'Laje (Térreo)',
            'Laje (Superior)',
            'Vigas e Pilares',
            'Piso Industrial',
            'Piso de Garagem / Estacionamento',
            'Fundação / Sapata / Bloco',
            'Calçada / Acesso',
            'Escada',
            'Capa de Laje (Treliçada)',
            'Enchimento de Piso',
            'Muro de Arrimo',
            'Radier',
        ];

        foreach ($locations as $location) {
            Location::create([
                'name' => $location,
                'is_active' => true,
            ]);
        }
    }
}
