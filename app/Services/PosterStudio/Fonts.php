<?php

namespace App\Services\PosterStudio;

/**
 * Fontes do editor (config/poster-studio.php → fonts).
 */
class Fonts
{
    public static function all(): array
    {
        return config('poster-studio.fonts');
    }

    /**
     * URL única do Google Fonts com todos os pesos (e itálicos, quando existem).
     */
    public static function googleUrl(): string
    {
        $families = collect(self::all())->map(function (array $font, string $family) {
            $name = str_replace(' ', '+', $family);
            $weights = $font['weights'];

            if (! $font['italic']) {
                return "family={$name}:wght@".implode(';', $weights);
            }

            $axes = collect([0, 1])->flatMap(fn ($italic) => array_map(fn ($w) => "{$italic},{$w}", $weights));

            return "family={$name}:ital,wght@".$axes->implode(';');
        });

        return 'https://fonts.googleapis.com/css2?'.$families->implode('&').'&display=swap';
    }
}
