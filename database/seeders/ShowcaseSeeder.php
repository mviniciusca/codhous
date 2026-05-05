<?php

namespace Database\Seeders;

use App\Models\Showcase;
use Illuminate\Database\Seeder;

class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        $obras = [
            [
                'title' => 'Residencial Viver Bem',
                'location' => 'Rio de Janeiro, RJ',
                'description' => 'Execução completa de fundação e estrutura para condomínio residencial com 12 pavimentos.',
                'images' => ['https://images.unsplash.com/photo-1541888946425-d81bb19480c5?auto=format&fit=crop&q=80&w=800'],
                'videos' => [['title' => 'Tour da Obra', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']],
            ],
            [
                'title' => 'Centro Logístico Speed',
                'location' => 'Duque de Caxias, RJ',
                'description' => 'Piso industrial de alta resistência com nivelamento a laser para grande centro de distribuição.',
                'images' => ['https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Edifício Corporate Tower',
                'location' => 'São Paulo, SP',
                'description' => 'Bombeamento de concreto de alta performance para estrutura metálica e lajes técnicas.',
                'images' => ['https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Shopping Park Sul',
                'location' => 'Volta Redonda, RJ',
                'description' => 'Fundação profunda e contenção para expansão de área comercial e estacionamento subterrâneo.',
                'images' => ['https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Hospital Santa Luzia',
                'location' => 'Niterói, RJ',
                'description' => 'Estrutura hospitalar complexa com especificações rigorosas de traço de concreto para áreas radiológicas.',
                'images' => ['https://images.unsplash.com/photo-1586771107445-d3ca888129ff?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Viaduto do Amanhã',
                'location' => 'Nova Iguaçu, RJ',
                'description' => 'Lançamento de vigas e concretagem de tabuleiro para obra de infraestrutura urbana de grande porte.',
                'images' => ['https://images.unsplash.com/photo-1513828583688-c52646db42da?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Condomínio Eco Life',
                'location' => 'Barra da Tijuca, RJ',
                'description' => 'Projeto sustentável com concreto de baixo impacto ambiental e reaproveitamento de água na cura.',
                'images' => ['https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Escola Técnica Estadual',
                'location' => 'Belford Roxo, RJ',
                'description' => 'Construção rápida utilizando sistemas de fôrmas deslizantes para agilizar a entrega do pavilhão principal.',
                'images' => ['https://images.unsplash.com/photo-1503387762-592dea58ef23?auto=format&fit=crop&q=80&w=800'],
            ],
            [
                'title' => 'Arena Poliesportiva',
                'location' => 'São Gonçalo, RJ',
                'description' => 'Concretagem de arquibancadas e áreas técnicas com acabamento em concreto aparente de alto padrão.',
                'images' => ['https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=800'],
            ],
        ];

        foreach ($obras as $obra) {
            Showcase::create(array_merge($obra, [
                'is_active' => true,
                'sort_order' => 0,
            ]));
        }
    }
}
