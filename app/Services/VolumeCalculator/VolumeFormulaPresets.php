<?php

namespace App\Services\VolumeCalculator;

use App\Models\VolumeFormula;

/**
 * Fórmulas que acompanham o sistema. A empresa pode ajustar margem, campos e
 * arredondamento (cada ajuste vira uma nova versão) e voltar ao padrão daqui.
 */
class VolumeFormulaPresets
{
    /** @return array<string, array{name: string, description: string, shape: string, sort: int, definition: array}> */
    public static function all(): array
    {
        return [
            'laje-piso' => [
                'name' => 'Lajes / Pisos',
                'description' => 'Lajes maciças, contrapisos, calçadas e radiers. Volume = largura × comprimento × espessura.',
                'shape' => 'slab',
                'sort' => 1,
                'definition' => [
                    'expression' => 'largura * comprimento * espessura',
                    'variables' => [
                        ['key' => 'largura', 'label' => 'Largura', 'unit' => 'm', 'example' => 5],
                        ['key' => 'comprimento', 'label' => 'Comprimento', 'unit' => 'm', 'example' => 8],
                        ['key' => 'espessura', 'label' => 'Espessura', 'unit' => 'cm', 'example' => 10, 'max' => 200, 'help' => 'Lajes maciças costumam ter de 8 a 12 cm; pisos, de 5 a 15 cm.'],
                    ],
                    'margin_percent' => 10,
                    'rounding' => 'none',
                    'rounding_step' => 0,
                ],
            ],
            'pilar-estaca-circular' => [
                'name' => 'Pilares / Estacas',
                'description' => 'Peças cilíndricas: estacas escavadas, tubulões sem base alargada e pilares redondos. Volume = π × (diâmetro ÷ 2)² × altura × quantidade.',
                'shape' => 'cylinder',
                'sort' => 2,
                'definition' => [
                    'expression' => 'pi * (diametro / 2)^2 * altura * quantidade',
                    'variables' => [
                        ['key' => 'diametro', 'label' => 'Diâmetro', 'unit' => 'cm', 'example' => 30, 'max' => 500, 'help' => 'Diâmetro nominal da estaca ou do pilar.'],
                        ['key' => 'altura', 'label' => 'Altura / profundidade', 'unit' => 'm', 'example' => 6],
                        ['key' => 'quantidade', 'label' => 'Quantidade', 'unit' => 'un', 'example' => 10, 'max' => 1000],
                    ],
                    'margin_percent' => 10,
                    'rounding' => 'none',
                    'rounding_step' => 0,
                ],
            ],
            'pilar-viga-retangular' => [
                'name' => 'Pilares / Vigas retangulares',
                'description' => 'Pilares, vigas, baldrames e blocos de seção retangular. Volume = largura × altura da seção × comprimento × quantidade.',
                'shape' => 'beam',
                'sort' => 3,
                'definition' => [
                    'expression' => 'largura * altura * comprimento * quantidade',
                    'variables' => [
                        ['key' => 'largura', 'label' => 'Largura da seção', 'unit' => 'cm', 'example' => 20, 'max' => 500],
                        ['key' => 'altura', 'label' => 'Altura da seção', 'unit' => 'cm', 'example' => 40, 'max' => 500],
                        ['key' => 'comprimento', 'label' => 'Comprimento', 'unit' => 'm', 'example' => 12, 'help' => 'Vão da viga ou altura do pilar.'],
                        ['key' => 'quantidade', 'label' => 'Quantidade', 'unit' => 'un', 'example' => 4, 'max' => 1000],
                    ],
                    'margin_percent' => 10,
                    'rounding' => 'none',
                    'rounding_step' => 0,
                ],
            ],
        ];
    }

    /** Cria as fórmulas padrão que ainda não existem (instalação nova ou migração) */
    public static function install(): void
    {
        foreach (self::all() as $slug => $preset) {
            if (VolumeFormula::where('slug', $slug)->exists()) {
                continue;
            }

            $formula = VolumeFormula::create([
                'slug' => $slug,
                'name' => $preset['name'],
                'description' => $preset['description'],
                'shape' => $preset['shape'],
                'sort' => $preset['sort'],
                'is_system' => true,
                'is_active' => true,
            ]);

            $formula->saveVersion(FormulaDefinition::fromArray($preset['definition']), null, 'Versão padrão do sistema');
        }
    }

    public static function definitionFor(string $slug): ?FormulaDefinition
    {
        $preset = self::all()[$slug] ?? null;

        return $preset ? FormulaDefinition::fromArray($preset['definition']) : null;
    }
}
