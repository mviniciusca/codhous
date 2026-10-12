<?php

namespace App\Services\PosterStudio;

use App\Models\PosterPalette;
use App\Models\Setting;

/**
 * Paletas semânticas: presets do config, a paleta da marca (derivada da cor
 * primária do site) e as paletas salvas pelos usuários ("custom-{id}").
 */
class Palettes
{
    public const BRAND = 'brand';

    public static function roles(): array
    {
        return array_keys(config('poster-studio.color_roles'));
    }

    /**
     * @return array<string, array{id:string,name:string,description:?string,colors:array<string,string>,custom?:bool}>
     */
    public static function all(): array
    {
        $palettes = [self::BRAND => self::brand()];

        foreach (config('poster-studio.palettes') as $id => $palette) {
            $palettes[$id] = ['id' => $id, ...$palette];
        }

        foreach (PosterPalette::query()->latest()->get() as $saved) {
            $id = 'custom-'.$saved->id;
            $palettes[$id] = [
                'id' => $id,
                'name' => $saved->name,
                'description' => 'Personalizada',
                'colors' => self::normalize($saved->colors),
                'custom' => true,
            ];
        }

        return $palettes;
    }

    public static function find(?string $id): ?array
    {
        return $id ? (self::all()[$id] ?? null) : null;
    }

    public static function default(): array
    {
        return self::find(config('poster-studio.default_palette')) ?? self::brand();
    }

    /**
     * Completa papéis ausentes e descarta valores inválidos, usando a paleta laranja como base.
     */
    public static function normalize(array $colors): array
    {
        $base = config('poster-studio.palettes.laranja.colors');
        $result = [];

        foreach (self::roles() as $role) {
            $value = $colors[$role] ?? null;
            $result[$role] = is_string($value) && self::isHex($value) ? strtoupper($value) : $base[$role];
        }

        return $result;
    }

    public static function isHex(string $value): bool
    {
        return (bool) preg_match('/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value);
    }

    /**
     * Paleta derivada da cor primária do site (settings.website.primary_color, "R G B").
     */
    public static function brand(): array
    {
        $hex = self::rgbStringToHex((string) Setting::get('website.primary_color', '')) ?? '#FF5A1F';

        return [
            'id' => self::BRAND,
            'name' => 'Da marca',
            'description' => 'Cor do site',
            'colors' => [
                'primary' => $hex,
                'secondary' => self::mix($hex, '#FFFFFF', 0.35),
                'accent' => self::mix($hex, '#000000', 0.25),
                'background' => '#FFFFFF',
                'surface' => self::mix($hex, '#FFFFFF', 0.92),
                'text' => '#111827',
                'muted' => '#4B5563',
                'onPrimary' => self::luminance($hex) > 0.6 ? '#111111' : '#FFFFFF',
                'dark' => self::mix($hex, '#0B0B0F', 0.85),
                'onDark' => '#FFFFFF',
            ],
        ];
    }

    public static function rgbStringToHex(string $rgb): ?string
    {
        if (self::isHex(trim($rgb))) {
            return strtoupper(trim($rgb));
        }

        $parts = preg_split('/[\s,]+/', trim($rgb));

        if (count($parts) !== 3 || collect($parts)->contains(fn ($p) => ! is_numeric($p) || $p < 0 || $p > 255)) {
            return null;
        }

        return sprintf('#%02X%02X%02X', ...array_map('intval', $parts));
    }

    public static function mix(string $hexA, string $hexB, float $weightB): string
    {
        [$r1, $g1, $b1] = sscanf(substr($hexA, 0, 7), '#%02x%02x%02x');
        [$r2, $g2, $b2] = sscanf(substr($hexB, 0, 7), '#%02x%02x%02x');

        return sprintf(
            '#%02X%02X%02X',
            round($r1 + ($r2 - $r1) * $weightB),
            round($g1 + ($g2 - $g1) * $weightB),
            round($b1 + ($b2 - $b1) * $weightB),
        );
    }

    public static function luminance(string $hex): float
    {
        [$r, $g, $b] = sscanf(substr($hex, 0, 7), '#%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }
}
