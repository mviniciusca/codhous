<?php

namespace App\Services\PosterStudio;

use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Templates editáveis em resources/poster-templates/*.json.
 *
 * Cada template é desenhado em 1080×1080 ("elements") e pode definir
 * ajustes de geometria por proporção em "layouts" (portrait, story, landscape),
 * indexados pelo id do elemento. Para adicionar um template basta criar o JSON.
 */
class Templates
{
    private static ?array $cache = null;

    public static function path(): string
    {
        return resource_path('poster-templates');
    }

    /**
     * @return array<string, array>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $templates = [];

        foreach (File::glob(self::path().'/*.json') as $file) {
            $template = json_decode(File::get($file), true);

            if (! is_array($template) || empty($template['id']) || ! isset($template['elements'])) {
                throw new RuntimeException('Template inválido: '.basename($file));
            }

            $templates[$template['id']] = $template;
        }

        uasort($templates, fn ($a, $b) => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

        return self::$cache = $templates;
    }

    public static function find(?string $id): ?array
    {
        return $id ? (self::all()[$id] ?? null) : null;
    }

    /**
     * Metadados para listagens (sem os elementos).
     */
    public static function catalog(): array
    {
        return array_values(array_map(fn (array $template) => [
            'id' => $template['id'],
            'name' => $template['name'],
            'category' => $template['category'] ?? 'Geral',
            'description' => $template['description'] ?? '',
            'tags' => $template['tags'] ?? [],
            'layouts' => array_keys($template['layouts'] ?? []),
        ], self::all()));
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
