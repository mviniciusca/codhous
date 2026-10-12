<?php

namespace App\Support;

use App\Models\Alert;

/**
 * Aparência dos alertas no site (x-site-alerts): paleta, ícone, rótulos do botão de
 * fechar e conteúdo demonstrativo de cada estilo. Um único lugar parametriza os seis
 * estilos nos três formatos (modal, toast e banner).
 *
 * Cores em "R G B" (usadas como rgb(var(--sa-*)) no CSS do componente):
 * - accent: ícone, bordas e destaques;
 * - button: botão principal no modal;
 * - banner: fundo da faixa;
 * - soft / ink: fundo e texto do card do modal.
 */
class AlertAppearance
{
    /** Ícones no traço do Lucide (o site usa Lucide; aqui inline para funcionar na prévia) */
    protected const ICONS = [
        'info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
        'gift' => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/>',
        'megaphone' => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'shield' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
        'triangle' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'check' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
    ];

    /** @return array<string, array<string, string>> */
    public static function styles(): array
    {
        return [
            Alert::STYLE_INFO => [
                'accent' => '37 99 235', 'button' => '37 99 235', 'banner' => '37 99 235',
                'soft' => '239 246 255', 'ink' => '15 23 42', 'icon' => 'info',
                'dismiss' => 'Fechar', 'dismissAlone' => 'Entendi',
                'demoTitle' => 'Manutenção programada',
                'demoMessage' => 'Nosso site passará por uma manutenção no dia 15/10, das 22h às 02h.',
            ],
            Alert::STYLE_PROMO => [
                'accent' => '124 58 237', 'button' => '109 40 217', 'banner' => '109 40 217',
                'soft' => '245 243 255', 'ink' => '30 27 75', 'icon' => 'gift',
                'dismiss' => 'Agora não', 'dismissAlone' => 'Entendi',
                'demoTitle' => 'Black Friday Codhous',
                'demoMessage' => 'Até 40% de desconto em serviços selecionados. Aproveite por tempo limitado!',
            ],
            Alert::STYLE_ANNOUNCEMENT => [
                'accent' => '234 128 12', 'button' => '217 119 6', 'banner' => '200 107 8',
                'soft' => '255 247 237', 'ink' => '67 32 4', 'icon' => 'megaphone',
                'dismiss' => 'Fechar', 'dismissAlone' => 'Entendi',
                'demoTitle' => 'Novo horário de atendimento',
                'demoMessage' => 'Nosso atendimento agora funciona de segunda a sexta, das 8h às 18h.',
            ],
            Alert::STYLE_CONSENT => [
                'accent' => '100 116 139', 'button' => '37 99 235', 'banner' => '51 65 85',
                'soft' => '241 245 249', 'ink' => '15 23 42', 'icon' => 'shield',
                'dismiss' => 'Recusar', 'dismissAlone' => 'Aceitar',
                'demoTitle' => 'Utilizamos cookies',
                'demoMessage' => 'Este site utiliza cookies para melhorar sua experiência.',
            ],
            Alert::STYLE_WARNING => [
                'accent' => '220 38 38', 'button' => '220 38 38', 'banner' => '185 28 28',
                'soft' => '254 242 242', 'ink' => '69 10 10', 'icon' => 'triangle',
                'dismiss' => 'Fechar', 'dismissAlone' => 'Entendi',
                'demoTitle' => 'Atenção!',
                'demoMessage' => 'Houve uma alteração importante nos prazos de entrega dos pedidos.',
            ],
            Alert::STYLE_SUCCESS => [
                'accent' => '22 163 74', 'button' => '21 128 61', 'banner' => '21 128 61',
                'soft' => '240 253 244', 'ink' => '5 46 22', 'icon' => 'check',
                'dismiss' => 'Fechar', 'dismissAlone' => 'Ok, entendi',
                'demoTitle' => 'Mensagem enviada com sucesso!',
                'demoMessage' => 'Entraremos em contato em breve para dar continuidade ao seu atendimento.',
            ],
        ];
    }

    /** @return array<string, string> */
    public static function for(?string $style): array
    {
        return static::styles()[$style] ?? static::styles()[Alert::STYLE_INFO];
    }

    /** Variáveis CSS do estilo, para o atributo style do alerta */
    public static function cssVariables(?string $style): string
    {
        $tokens = static::for($style);

        return collect(['accent', 'button', 'banner', 'soft', 'ink'])
            ->map(fn (string $key): string => "--sa-{$key}: {$tokens[$key]};")
            ->implode(' ');
    }

    public static function icon(?string $style, string $class = ''): string
    {
        $paths = self::ICONS[static::for($style)['icon']];

        return '<svg class="'.e($class).'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths.'</svg>';
    }
}
