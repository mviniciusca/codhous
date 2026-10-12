<?php

namespace App\Services\VolumeCalculator;

use App\Models\Setting;

/**
 * Pedido mínimo do caminhão betoneira. É regra de entrega da empresa, não da
 * geometria: fica fora das fórmulas e só gera uma recomendação quando o volume
 * calculado não completa o mínimo. Configurado em settings.company.concrete_delivery
 * (Ferramentas › Fórmulas de volume › Pedido mínimo).
 */
class TruckMinimum
{
    public const DEFAULT_VOLUME = 3.0;

    public const SETTING = 'company.concrete_delivery';

    /** @return array{enabled: bool, min_volume: float} */
    public static function settings(): array
    {
        $saved = (array) Setting::get(self::SETTING, []);

        return [
            'enabled' => (bool) ($saved['truck_minimum_enabled'] ?? true),
            'min_volume' => (float) ($saved['truck_minimum_volume'] ?? self::DEFAULT_VOLUME),
        ];
    }

    public static function save(bool $enabled, float $minVolume): void
    {
        $setting = Setting::first() ?? new Setting(['settings' => []]);
        $setting->settings = Setting::merge($setting->settings ?? [], [
            'company' => ['concrete_delivery' => [
                'truck_minimum_enabled' => $enabled,
                'truck_minimum_volume' => round($minVolume, 2),
            ]],
        ]);
        $setting->save();

        // Setting::get() memoriza a linha por requisição
        Setting::endPreview();
    }

    /**
     * Recomendação quando o volume não completa o caminhão; null quando completa
     * ou quando a regra está desligada.
     *
     * @return array{min_volume: float, missing: float, suggestions: array<int, array{icon: string, lucide: string, title: string, text: string}>}|null
     */
    public static function advise(float $volume): ?array
    {
        ['enabled' => $enabled, 'min_volume' => $min] = self::settings();

        if (! $enabled || $min <= 0 || $volume <= 0 || round($volume, 6) >= round($min, 6)) {
            return null;
        }

        $missing = round($min - $volume, 2);
        $minLabel = CalculationResult::formatVolume($min);

        return [
            'min_volume' => $min,
            'missing' => $missing,
            'suggestions' => [
                [
                    'icon' => 'heroicon-m-arrow-path',
                    'lucide' => 'refresh-cw',
                    'title' => 'Virar o concreto na obra',
                    'text' => 'Para volumes pequenos, preparar com betoneira na própria obra costuma sair mais em conta que um caminhão.',
                ],
                [
                    'icon' => 'heroicon-m-archive-box',
                    'lucide' => 'package',
                    'title' => 'Usar concreto ensacado',
                    'text' => 'Pronto para misturar com água; prático para reparos, bases e peças pequenas.',
                ],
                [
                    'icon' => 'heroicon-m-squares-plus',
                    'lucide' => 'layers',
                    'title' => 'Juntar com outra etapa da obra',
                    'text' => 'Programar esta concretagem junto com outra (calçada, contrapiso, baldrame) para completar '.$minLabel.'.',
                ],
                [
                    'icon' => 'heroicon-m-chat-bubble-left-right',
                    'lucide' => 'message-circle',
                    'title' => 'Consultar a concreteira',
                    'text' => 'Algumas aceitam volume menor cobrando o mínimo ou uma taxa de complemento.',
                ],
            ],
        ];
    }
}
