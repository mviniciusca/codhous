<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipments = [
            ['name' => 'Alisadora de Concreto 36"', 'category' => 'Acabamento de Piso', 'description' => 'Equipamento ideal para dar acabamento perfeito em pisos de concreto.', 'is_available' => true],
            ['name' => 'Alisadora de Concreto 46"', 'category' => 'Acabamento de Piso', 'description' => 'Bailarina de grande porte para alisamento de grandes áreas de concreto.', 'is_available' => true],
            ['name' => 'Cortadora de Piso', 'category' => 'Acabamento de Piso', 'description' => 'Máquina para corte de juntas de dilatação em pisos de concreto e asfalto.', 'is_available' => true],
            ['name' => 'Régua Vibratória', 'category' => 'Acabamento de Piso', 'description' => 'Utilizada para adensamento e nivelamento de pisos de concreto.', 'is_available' => true],
            
            ['name' => 'Politriz de Piso', 'category' => 'Preparação de Piso', 'description' => 'Ideal para polimento e desbaste de pisos de concreto e granilite.', 'is_available' => true],
            ['name' => 'Escarificador de Piso', 'category' => 'Preparação de Piso', 'description' => 'Remove camadas superficiais de concreto, tintas e resinas.', 'is_available' => true],
            ['name' => 'Fresadora de Piso', 'category' => 'Preparação de Piso', 'description' => 'Equipamento para remoção rápida de grandes volumes de concreto ou asfalto.', 'is_available' => true],
            ['name' => 'Lixadeira de Piso', 'category' => 'Preparação de Piso', 'description' => 'Lixadeira industrial para preparação de superfícies antes da pintura.', 'is_available' => true],

            ['name' => 'Lavadora de Alta Pressão', 'category' => 'Limpeza Industrial', 'description' => 'Lavadora profissional para limpeza pesada de obras e galpões.', 'is_available' => true],
            ['name' => 'Aspirador de Pó Industrial', 'category' => 'Limpeza Industrial', 'description' => 'Aspirador de alta potência para sólidos e líquidos em ambientes de obra.', 'is_available' => true],
            ['name' => 'Varredeira Industrial', 'category' => 'Limpeza Industrial', 'description' => 'Varredeira mecânica para grandes galpões e áreas externas.', 'is_available' => true],

            ['name' => 'Caminhão Pipa', 'category' => 'Transporte de Água', 'description' => 'Transporte de água potável ou de reuso para canteiros de obra.', 'is_available' => true],
            ['name' => 'Bomba Submersa', 'category' => 'Transporte de Água', 'description' => 'Bomba para drenagem de água limpa ou suja em valas e fundações.', 'is_available' => true],
            ['name' => 'Bomba Sapo', 'category' => 'Transporte de Água', 'description' => 'Bomba vibratória para poços e cisternas.', 'is_available' => true],
        ];

        foreach ($equipments as $item) {
            Equipment::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
