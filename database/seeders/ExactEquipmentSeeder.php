<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class ExactEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        Equipment::truncate();

        $equipments = [
            ['name' => 'Máquina Industrial Shot Blast Azul', 'category' => 'Preparação de Piso', 'description' => 'Ideal para preparação de grandes superfícies de piso.', 'is_available' => true],
            ['name' => 'Desempenadeira de Concreto Amarela Profissional', 'category' => 'Acabamento de Piso', 'description' => 'Acabamento profissional para pisos de concreto.', 'is_available' => true],
            ['name' => 'Aspirador Industrial HEPA Amarelo e Cinza', 'category' => 'Limpeza Industrial', 'description' => 'Aspirador de alta performance para uso em obras.', 'is_available' => true],
            ['name' => 'Caminhão-tanque branco em estúdio', 'category' => 'Transporte de Água', 'description' => 'Caminhão tanque para fornecimento de água.', 'is_available' => true],
            ['name' => 'Usina Industrial de Concreto em Fundo Branco', 'category' => 'Equipamentos Pesados', 'description' => 'Usina misturadora de concreto de alta capacidade.', 'is_available' => true],
            ['name' => 'Nível a Laser Industrial Amarelo com Feixe Verde', 'category' => 'Acabamento de Piso', 'description' => 'Nível a laser de alta precisão.', 'is_available' => true],
            ['name' => 'Politriz Industrial de Piso em Estúdio', 'category' => 'Preparação de Piso', 'description' => 'Para polimento e desbaste de pisos industriais.', 'is_available' => true],
            ['name' => 'Serra de Piso Laranja Industrial', 'category' => 'Acabamento de Piso', 'description' => 'Para corte de juntas de dilatação.', 'is_available' => true],
            ['name' => 'Desempenadeira de concreto com cabo azul', 'category' => 'Acabamento de Piso', 'description' => 'Desempenadeira manual para alisamento de concreto.', 'is_available' => true],
            ['name' => 'Alisadora de Concreto Amarela Industrial', 'category' => 'Acabamento de Piso', 'description' => 'Acabamento rápido e uniforme em pisos de concreto.', 'is_available' => true],
            ['name' => 'Régua vibratória industrial em estúdio', 'category' => 'Acabamento de Piso', 'description' => 'Adensamento de pequenas e médias áreas de concreto.', 'is_available' => true],
            ['name' => 'Caminhão lança-betoneira laranja e branco', 'category' => 'Equipamentos Pesados', 'description' => 'Caminhão bomba lança para concretagem.', 'is_available' => true],
            ['name' => 'Caminhão Betoneira Branco com Faixas Laranja', 'category' => 'Equipamentos Pesados', 'description' => 'Transporte e mistura de concreto usinado.', 'is_available' => true],
        ];

        foreach ($equipments as $item) {
            Equipment::create($item);
        }
    }
}
