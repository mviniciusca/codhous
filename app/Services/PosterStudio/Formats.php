<?php

namespace App\Services\PosterStudio;

use InvalidArgumentException;

/**
 * Formatos de publicação (config/poster-studio.php → formats).
 */
class Formats
{
    public const CUSTOM = 'custom';

    public static function all(): array
    {
        return config('poster-studio.formats');
    }

    public static function exists(string $key): bool
    {
        return $key === self::CUSTOM || array_key_exists($key, self::all());
    }

    /**
     * @return array{width:int,height:int,group:?string}
     */
    public static function dimensions(string $key, ?int $width = null, ?int $height = null): array
    {
        if ($key === self::CUSTOM) {
            $min = config('poster-studio.custom_format.min');
            $max = config('poster-studio.custom_format.max');

            if ($width === null || $height === null || $width < $min || $height < $min || $width > $max || $height > $max) {
                throw new InvalidArgumentException("Dimensões personalizadas devem estar entre {$min} e {$max} px.");
            }

            return ['width' => $width, 'height' => $height, 'group' => null];
        }

        $format = self::all()[$key] ?? throw new InvalidArgumentException("Formato desconhecido: {$key}");

        return ['width' => $format['width'], 'height' => $format['height'], 'group' => $format['group']];
    }

    public static function label(string $key): string
    {
        return $key === self::CUSTOM ? 'Personalizado' : (self::all()[$key]['label'] ?? $key);
    }
}
