<?php

namespace App\Services\PosterStudio;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Documento gráfico serializável do Estúdio de Posts (schema v1).
 *
 * {
 *   version, templateId, format, width, height,
 *   palette: {id, name, colors: {role: hex}},
 *   background: ColorRef,
 *   elements: [Element],          // ordem = camadas (primeiro fica atrás)
 *   caption: string
 * }
 *
 * ColorRef = {"role": "primary"} (vinculada à paleta) | "#RRGGBB" (personalizada) | null (transparente).
 * Element  = id, type (text|image|rect|ellipse|icon|qrcode), name, x, y, width, height,
 *            angle, opacity, locked, hidden, bind?, bindTemplate? + propriedades do tipo.
 *
 * Imagens são referências a arquivos (/storage/..., /img/...), nunca base64.
 */
class PosterDocument
{
    public const TYPES = ['text', 'image', 'rect', 'ellipse', 'icon', 'qrcode'];

    private const COMMON_NUMERIC = ['x', 'y', 'width', 'height', 'angle', 'opacity'];

    private const TYPE_PROPS = [
        'text' => ['text', 'fontFamily', 'fontSize', 'fontWeight', 'fontStyle', 'textAlign', 'lineHeight', 'charSpacing', 'fill', 'uppercase'],
        'image' => ['src', 'assetId', 'fit', 'radius', 'brightness', 'contrast', 'saturation', 'flipX', 'placeholder'],
        'rect' => ['fill', 'stroke', 'strokeWidth', 'radius'],
        'ellipse' => ['fill', 'stroke', 'strokeWidth'],
        'icon' => ['icon', 'fill', 'strokeWidth'],
        'qrcode' => ['value', 'fill', 'background'],
    ];

    /**
     * Documento vazio no formato pedido.
     */
    public static function blank(string $format, ?array $palette = null, ?int $width = null, ?int $height = null): array
    {
        $dimensions = Formats::dimensions($format, $width, $height);
        $palette ??= Palettes::default();

        return [
            'version' => config('poster-studio.schema_version'),
            'templateId' => null,
            'format' => $format,
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'palette' => self::paletteRef($palette),
            'background' => ['role' => 'background'],
            'elements' => [],
            'caption' => '',
        ];
    }

    /**
     * Cria o documento a partir de um template: monta o layout base (quadrado),
     * adapta ao formato pedido e preenche os vínculos com os dados da empresa.
     */
    public static function fromTemplate(array $template, string $format, ?array $palette, BrandKit $brand, ?int $width = null, ?int $height = null): array
    {
        $palette ??= Palettes::default();

        $document = [
            'version' => config('poster-studio.schema_version'),
            'templateId' => $template['id'],
            'format' => 'instagram_square',
            'width' => 1080,
            'height' => 1080,
            'palette' => self::paletteRef($palette),
            'background' => $template['background'] ?? ['role' => 'background'],
            'elements' => $template['elements'],
            'caption' => $brand->interpolate($template['caption'] ?? ''),
        ];

        $document = FormatAdapter::adapt($document, $format, $template, $width, $height);

        return self::validate(self::resolveBindings($document, $brand));
    }

    /**
     * Preenche elementos vinculados (texto, imagem e QR Code) com os valores atuais da empresa.
     * Vínculos sem valor mantêm o conteúdo do template, que serve de exemplo editável.
     */
    public static function resolveBindings(array $document, BrandKit $brand): array
    {
        foreach ($document['elements'] as &$element) {
            $key = $element['bind'] ?? null;
            $value = $key ? $brand->value($key) : null;

            if ($value === null) {
                continue;
            }

            match ($element['type']) {
                'text' => $element['text'] = str_replace('{value}', $value, $element['bindTemplate'] ?? '{value}'),
                'image' => [$element['src'] = $value, $element['placeholder'] = false],
                'qrcode' => $element['value'] = $value,
                default => null,
            };
        }

        return $document;
    }

    /**
     * Troca a paleta. Elementos com cor personalizada (hex) não são alterados.
     */
    public static function withPalette(array $document, array $palette): array
    {
        $document['palette'] = self::paletteRef($palette);

        return $document;
    }

    /**
     * Valida e normaliza um documento vindo do navegador. Remove propriedades
     * desconhecidas e lança ValidationException para estruturas inválidas.
     */
    public static function validate(array $document): array
    {
        $errors = [];
        $limits = config('poster-studio.limits');

        $format = (string) ($document['format'] ?? '');
        if (! Formats::exists($format)) {
            $errors['document.format'] = 'Formato de publicação inválido.';
        }

        $min = config('poster-studio.custom_format.min');
        $max = config('poster-studio.custom_format.max');
        $width = (int) ($document['width'] ?? 0);
        $height = (int) ($document['height'] ?? 0);

        if ($width < $min || $width > $max || $height < $min || $height > $max) {
            $errors['document.width'] = "Dimensões devem estar entre {$min} e {$max} px.";
        }

        $elements = $document['elements'] ?? [];
        if (! is_array($elements) || ! array_is_list($elements)) {
            $errors['document.elements'] = 'Lista de elementos inválida.';
            $elements = [];
        }

        if (count($elements) > $limits['elements']) {
            $errors['document.elements'] = "Limite de {$limits['elements']} elementos por arte.";
        }

        $clean = [];
        $ids = [];

        foreach ($elements as $index => $element) {
            $result = self::validateElement($element, $index);

            if (is_string($result)) {
                $errors["document.elements.{$index}"] = $result;

                continue;
            }

            if (isset($ids[$result['id']])) {
                $result['id'] = self::newId();
            }

            $ids[$result['id']] = true;
            $clean[] = $result;
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $palette = is_array($document['palette'] ?? null) ? $document['palette'] : [];

        return [
            'version' => config('poster-studio.schema_version'),
            'templateId' => is_string($document['templateId'] ?? null) ? Str::limit($document['templateId'], 80, '') : null,
            'format' => $format,
            'width' => $width,
            'height' => $height,
            'palette' => [
                'id' => is_string($palette['id'] ?? null) ? Str::limit($palette['id'], 60, '') : 'custom',
                'name' => is_string($palette['name'] ?? null) ? Str::limit($palette['name'], 60, '') : 'Personalizada',
                'colors' => Palettes::normalize(is_array($palette['colors'] ?? null) ? $palette['colors'] : []),
            ],
            'background' => array_key_exists('background', $document) ? self::colorRef($document['background']) : ['role' => 'background'],
            'elements' => $clean,
            'caption' => Str::limit((string) ($document['caption'] ?? ''), 2200, ''),
        ];
    }

    public static function newId(): string
    {
        return 'el_'.Str::lower(Str::random(10));
    }

    public static function paletteRef(array $palette): array
    {
        return [
            'id' => $palette['id'] ?? 'custom',
            'name' => $palette['name'] ?? 'Personalizada',
            'colors' => Palettes::normalize($palette['colors'] ?? []),
        ];
    }

    /**
     * Converte um ColorRef em hex usando a paleta do documento.
     */
    public static function resolveColor(mixed $ref, array $document): ?string
    {
        if (is_array($ref) && isset($ref['role'])) {
            return $document['palette']['colors'][$ref['role']] ?? null;
        }

        return is_string($ref) ? $ref : null;
    }

    /**
     * @return array|string Elemento normalizado ou mensagem de erro.
     */
    private static function validateElement(mixed $element, int $index): array|string
    {
        if (! is_array($element)) {
            return 'Elemento inválido.';
        }

        $type = $element['type'] ?? null;
        if (! in_array($type, self::TYPES, true)) {
            return 'Tipo de elemento não suportado.';
        }

        $id = (string) ($element['id'] ?? '');
        $clean = [
            'id' => preg_match('/^[A-Za-z0-9_\-]{1,40}$/', $id) ? $id : self::newId(),
            'type' => $type,
            'name' => Str::limit(strip_tags((string) ($element['name'] ?? ucfirst($type))), 60, ''),
        ];

        foreach (self::COMMON_NUMERIC as $key) {
            $value = $element[$key] ?? null;

            if ($value !== null && ! is_numeric($value)) {
                return "Valor numérico inválido em {$key}.";
            }

            $clean[$key] = round((float) ($value ?? ($key === 'opacity' ? 1 : 0)), 2);
        }

        $clean['width'] = max(1, min(20000, $clean['width']));
        $clean['height'] = max(1, min(20000, $clean['height']));
        $clean['x'] = max(-20000, min(20000, $clean['x']));
        $clean['y'] = max(-20000, min(20000, $clean['y']));
        $clean['opacity'] = max(0, min(1, $clean['opacity']));
        $clean['angle'] = fmod($clean['angle'], 360);
        $clean['locked'] = (bool) ($element['locked'] ?? false);
        $clean['hidden'] = (bool) ($element['hidden'] ?? false);

        $bind = $element['bind'] ?? null;
        $clean['bind'] = is_string($bind) && array_key_exists($bind, BrandKit::BINDINGS) ? $bind : null;

        if ($clean['bind'] && isset($element['bindTemplate']) && is_string($element['bindTemplate'])) {
            $clean['bindTemplate'] = Str::limit($element['bindTemplate'], 200, '');
        }

        foreach (self::TYPE_PROPS[$type] as $prop) {
            if (! array_key_exists($prop, $element)) {
                continue;
            }

            $value = self::validateProp($prop, $element[$prop]);

            if ($value instanceof \Throwable) {
                return $value->getMessage();
            }

            $clean[$prop] = $value;
        }

        if ($type === 'text' && ! isset($clean['text'])) {
            $clean['text'] = '';
        }

        if ($type === 'image' && empty($clean['src'])) {
            $clean['src'] = null;
            $clean['placeholder'] = true;
        }

        return $clean;
    }

    private static function validateProp(string $prop, mixed $value): mixed
    {
        $fonts = array_keys(config('poster-studio.fonts'));

        return match ($prop) {
            'text' => Str::limit((string) $value, config('poster-studio.limits.text_length'), ''),
            'value' => Str::limit((string) $value, 500, ''),
            'fontFamily' => in_array($value, $fonts, true) ? $value : $fonts[0],
            'fontWeight' => in_array((int) $value, [100, 200, 300, 400, 500, 600, 700, 800, 900], true) ? (int) $value : 400,
            'fontStyle' => $value === 'italic' ? 'italic' : 'normal',
            'textAlign' => in_array($value, ['left', 'center', 'right', 'justify'], true) ? $value : 'left',
            'fit' => in_array($value, ['cover', 'contain'], true) ? $value : 'cover',
            'fontSize' => self::number($value, 4, 1000),
            'lineHeight' => self::number($value, 0.5, 4),
            'charSpacing' => self::number($value, -500, 2000),
            'strokeWidth' => self::number($value, 0, 200),
            'radius' => self::number($value, 0, 5000),
            'brightness', 'contrast', 'saturation' => self::number($value, -1, 1),
            'uppercase', 'flipX', 'placeholder' => (bool) $value,
            'assetId' => is_numeric($value) ? (int) $value : null,
            'icon' => is_string($value) && preg_match('/^[A-Za-z0-9]{1,60}$/', $value) ? $value : 'Star',
            'fill', 'stroke', 'background' => self::colorRef($value),
            'src' => self::src($value),
            default => null,
        };
    }

    private static function number(mixed $value, float $min, float $max): float|\Throwable
    {
        if (! is_numeric($value)) {
            return new \InvalidArgumentException('Valor numérico inválido.');
        }

        return round(max($min, min($max, (float) $value)), 3);
    }

    /**
     * Normaliza um ColorRef: papel da paleta, hex personalizado ou null (transparente).
     */
    public static function colorRef(mixed $value): array|string|null
    {
        if (is_string($value) && Palettes::isHex($value)) {
            return strtoupper($value);
        }

        if (is_array($value) && isset($value['role']) && in_array($value['role'], Palettes::roles(), true)) {
            return ['role' => $value['role']];
        }

        return null;
    }

    private static function src(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '' && Str::startsWith($value, $appUrl.'/')) {
            $value = Str::after($value, $appUrl);
        }

        $path = (string) parse_url($value, PHP_URL_PATH);

        // Apenas arquivos servidos pelo próprio app: evita recursos externos (canvas "contaminado" na exportação)
        if (! preg_match('#^/(storage|img)/[^?\#]+\.(png|jpe?g|webp|gif|svg)$#i', $path) || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }
}
