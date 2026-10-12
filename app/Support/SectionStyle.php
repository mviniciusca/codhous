<?php

namespace App\Support;

/**
 * Resolve o visual de uma seção do Page Builder (fundo, esquema de cor e
 * espaçamento vertical) a partir dos dados do bloco.
 *
 * As classes ficam escritas por extenso aqui para o Tailwind encontrá-las
 * (app/Support está no "content" do tailwind.config.js).
 */
final class SectionStyle
{
    /** Escala de espaçamento vertical, do padrão do Tailwind, responsiva. */
    public const SPACING_LABELS = [
        'none' => '0',
        'xs' => 'XS',
        'sm' => 'P',
        'md' => 'M',
        'lg' => 'G',
        'xl' => 'XG',
    ];

    private const SPACING_TOP = [
        'none' => 'pt-0',
        'xs' => 'pt-6 md:pt-8',
        'sm' => 'pt-10 md:pt-12',
        'md' => 'pt-14 md:pt-20',
        'lg' => 'pt-20 md:pt-28',
        'xl' => 'pt-24 md:pt-36',
    ];

    private const SPACING_BOTTOM = [
        'none' => 'pb-0',
        'xs' => 'pb-6 md:pb-8',
        'sm' => 'pb-10 md:pb-12',
        'md' => 'pb-14 md:pb-20',
        'lg' => 'pb-20 md:pb-28',
        'xl' => 'pb-24 md:pb-36',
    ];

    /**
     * Classes de padding vertical. Cada lado usa o valor do bloco ou o padrão informado.
     */
    public static function spacing(array $data, string $default = 'md'): string
    {
        $top = $data['spacing_top'] ?? null;
        $bottom = $data['spacing_bottom'] ?? null;

        $topClass = self::SPACING_TOP[$top] ?? self::SPACING_TOP[$default] ?? self::SPACING_TOP['md'];
        $bottomClass = self::SPACING_BOTTOM[$bottom] ?? self::SPACING_BOTTOM[$default] ?? self::SPACING_BOTTOM['md'];

        return "{$topClass} {$bottomClass}";
    }

    /**
     * Esquema de cor dos textos: "light" (texto escuro), "dark" (texto claro) ou "primary" (sobre a cor de destaque).
     *
     * Fundos escuro e primário escolhem o esquema sozinhos; nos demais vale a opção do bloco.
     */
    public static function scheme(array $data, ?string $background = null): string
    {
        $background ??= (string) ($data['background_color'] ?? '');

        if (($data['text_color'] ?? 'light') === 'dark' || str_contains($background, 'bg-foreground')) {
            return 'dark';
        }

        if (preg_match('/(^|\s)bg-primary(\s|$)/', $background)) {
            return 'primary';
        }

        return 'light';
    }

    /**
     * Classe CSS que aplica o esquema (redefine os tokens de cor da seção).
     */
    public static function schemeClass(string $scheme): string
    {
        return match ($scheme) {
            'dark' => 'text-scheme-dark',
            'primary' => 'scheme-primary',
            default => '',
        };
    }
}
