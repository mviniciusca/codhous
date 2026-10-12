<?php

namespace App\Services\PosterStudio;

/**
 * Adapta um documento a outro formato sem esticar a arte.
 *
 * 1. Elementos que cobrem o canvas inteiro (fundos, fotos de fundo) são esticados.
 * 2. Faixas de largura/altura total acompanham a nova largura/altura.
 * 3. Os demais são escalados proporcionalmente e ancorados ao terço do canvas
 *    em que estão (início, meio ou fim), preservando margens.
 * 4. Se o documento veio de um template que define um layout para a proporção
 *    de destino, a geometria desse layout tem prioridade.
 */
class FormatAdapter
{
    private const FULL = 0.9;

    private const SCALED_PROPS = ['fontSize', 'strokeWidth', 'radius'];

    private const LAYOUT_PROPS = ['x', 'y', 'width', 'height', 'angle', 'fontSize', 'radius', 'strokeWidth', 'hidden', 'textAlign'];

    public static function adapt(array $document, string $format, ?array $template = null, ?int $width = null, ?int $height = null): array
    {
        $target = Formats::dimensions($format, $width, $height);

        $fromW = (float) $document['width'];
        $fromH = (float) $document['height'];
        $toW = (float) $target['width'];
        $toH = (float) $target['height'];

        $document['format'] = $format;
        $document['width'] = (int) $toW;
        $document['height'] = (int) $toH;

        if ($fromW === $toW && $fromH === $toH) {
            return self::applyTemplateLayout($document, $template, $target['group']);
        }

        $scale = min($toW / $fromW, $toH / $fromH);

        foreach ($document['elements'] as &$element) {
            $fullWidth = $element['width'] >= $fromW * self::FULL;
            $fullHeight = $element['height'] >= $fromH * self::FULL;

            if ($fullWidth && $fullHeight) {
                $element['x'] = $element['x'] * $toW / $fromW;
                $element['y'] = $element['y'] * $toH / $fromH;
                $element['width'] = $element['width'] * $toW / $fromW;
                $element['height'] = $element['height'] * $toH / $fromH;

                continue;
            }

            $newWidth = $fullWidth ? $element['width'] * $toW / $fromW : $element['width'] * $scale;
            $newHeight = $fullHeight ? $element['height'] * $toH / $fromH : $element['height'] * $scale;

            $element['x'] = $fullWidth
                ? $element['x'] * $toW / $fromW
                : self::anchor($element['x'], $element['width'], $fromW, $toW, $newWidth, $scale);
            $element['y'] = $fullHeight
                ? $element['y'] * $toH / $fromH
                : self::anchor($element['y'], $element['height'], $fromH, $toH, $newHeight, $scale);
            $element['width'] = $newWidth;
            $element['height'] = $newHeight;

            foreach (self::SCALED_PROPS as $prop) {
                if (isset($element[$prop]) && is_numeric($element[$prop])) {
                    $element[$prop] = $element[$prop] * $scale;
                }
            }
        }
        unset($element);

        $document['elements'] = array_map(fn ($element) => self::round($element), $document['elements']);

        return self::applyTemplateLayout($document, $template, $target['group']);
    }

    /**
     * Posição no novo eixo, mantendo a âncora (início, centro ou fim) do terço onde o elemento está.
     */
    private static function anchor(float $start, float $size, float $from, float $to, float $newSize, float $scale): float
    {
        $center = $start + $size / 2;

        if ($center < $from / 3) {
            return $start * $scale;
        }

        if ($center > $from * 2 / 3) {
            return $to - ($from - $start - $size) * $scale - $newSize;
        }

        return $to / 2 + ($center - $from / 2) * $scale - $newSize / 2;
    }

    private static function applyTemplateLayout(array $document, ?array $template, ?string $group): array
    {
        $layout = $group && $template && ($document['templateId'] ?? null) === ($template['id'] ?? null)
            ? ($template['layouts'][$group] ?? null)
            : null;

        if (! $layout) {
            return $document;
        }

        foreach ($document['elements'] as &$element) {
            $override = $layout[$element['id']] ?? null;

            if (is_array($override)) {
                $element = array_merge($element, array_intersect_key($override, array_flip(self::LAYOUT_PROPS)));
            }
        }

        return $document;
    }

    private static function round(array $element): array
    {
        foreach (['x', 'y', 'width', 'height', ...self::SCALED_PROPS] as $prop) {
            if (isset($element[$prop]) && is_numeric($element[$prop])) {
                $element[$prop] = round($element[$prop], 2);
            }
        }

        return $element;
    }
}
