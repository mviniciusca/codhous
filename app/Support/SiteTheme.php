<?php

namespace App\Support;

use App\Models\Setting;
use Filament\Support\Colors\Color;

/**
 * Tema do site público.
 *
 * A cor de destaque vem de settings.website.primary_color ("R G B"). Daqui saem
 * só as variáveis que dependem de cálculo: a cor base, a cor de texto sobre ela
 * (clara ou escura, conforme o contraste) e o tom "ink" seguro para texto em
 * fundo claro. Os tons 400–950 são derivados em CSS (resources/css/app.css),
 * então um tema futuro só precisa trocar variáveis.
 *
 * A mesma lógica existe em JS na ponte de prévia do painel
 * (filament/design/preview-bridge) para aplicar a cor sem recarregar.
 */
final class SiteTheme
{
    public const DEFAULT_PRIMARY = '249 115 22';

    /** Acima desta luminância a cor base recebe texto escuro (ex.: amarelo, lima, âmbar). */
    private const LIGHT_LUMINANCE = 0.42;

    /** Contraste mínimo do tom "ink" sobre branco (texto em destaque, kickers, links). */
    private const INK_CONTRAST = 4.0;

    /** Fator de mistura com preto de cada tom (espelha app.css). */
    private const SHADE_MIX = [500 => 1.0, 600 => 0.86, 700 => 0.70, 800 => 0.56, 900 => 0.44];

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    public static function parse(?string $value): array
    {
        $value = trim((string) $value);

        if (preg_match('/^#?([0-9a-f]{6})$/i', $value, $hex)) {
            return array_map('hexdec', str_split($hex[1], 2));
        }

        preg_match_all('/\d{1,3}/', $value, $matches);
        $channels = array_map(fn ($c) => min(255, (int) $c), $matches[0]);

        return count($channels) >= 3
            ? array_slice($channels, 0, 3)
            : self::parse(self::DEFAULT_PRIMARY);
    }

    /**
     * Variáveis CSS do tema, prontas para um bloco <style>.
     */
    public static function css(?string $primary = null): string
    {
        $rgb = self::parse($primary ?? Setting::get('website.primary_color', self::DEFAULT_PRIMARY));
        $luminance = self::luminance($rgb);

        $vars = [
            '--primary-color' => implode(' ', $rgb),
            '--color-primary-foreground' => $luminance > self::LIGHT_LUMINANCE ? 'oklch(0.2 0.02 60)' : 'oklch(1 0 0)',
            '--color-primary-ink-base' => 'var(--color-primary-'.self::inkShade($luminance).')',
        ];

        // Paleta no formato do Filament ("r, g, b") para os componentes de formulário usados no site
        foreach (Color::rgb('rgb('.implode(', ', $rgb).')') as $shade => $channels) {
            $vars["--primary-{$shade}"] = $channels;
        }
        foreach (Color::Gray as $shade => $channels) {
            $vars["--gray-{$shade}"] = $channels;
        }

        return ':root{'.collect($vars)->map(fn ($v, $k) => "{$k}:{$v}")->implode(';').'}';
    }

    /**
     * Primeiro tom (500→900) com contraste suficiente sobre branco.
     */
    public static function inkShade(float $luminance): int
    {
        foreach (self::SHADE_MIX as $shade => $mix) {
            // Misturar com preto em oklab escala L; a luminância relativa acompanha L³.
            $shadeLuminance = $luminance * $mix ** 3;

            if (1.05 / ($shadeLuminance + 0.05) >= self::INK_CONTRAST) {
                return $shade;
            }
        }

        return 900;
    }

    /**
     * Luminância relativa (WCAG).
     *
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    public static function luminance(array $rgb): float
    {
        [$r, $g, $b] = array_map(function (int $c): float {
            $c /= 255;

            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }, $rgb);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
