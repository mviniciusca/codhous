<?php

namespace App\Enums;

enum CardPreset: string
{
    case MAX = 'max';             // Impacto, Poppins, Esquerda
    case FLUX = 'flux';           // Elegante, Fina, Centralizado
    case CANVA_SIDE = 'canva_side'; // Bloco lateral branco/preto
    case BOLD_CENTER = 'bold_center'; // Clássico centralizado pesado
    case MINIMAL = 'minimal';     // Texto pequeno, elegante, cantos
    case STACKED = 'stacked';     // Título e Subtítulo empilhados com destaque

    public function label(): string
    {
        return match($this) {
            self::MAX => 'Estilo MAX (Impacto)',
            self::FLUX => 'Estilo FLUX (Elegante)',
            self::CANVA_SIDE => 'Canva Side Block',
            self::BOLD_CENTER => 'Bold Center',
            self::MINIMAL => 'Minimalist Clean',
            self::STACKED => 'Stacked Blocks',
        };
    }

    /**
     * Retorna as configurações de estilo do preset.
     */
    public function getStyle(): array
    {
        return match($this) {
            self::MAX => [
                'font' => 'Montserrat',
                'align' => 'left',
                'valign' => 'center',
                'uppercase' => true,
                'has_block' => false,
            ],
            self::FLUX => [
                'font' => 'Playfair+Display',
                'align' => 'center',
                'valign' => 'center',
                'uppercase' => false,
                'has_block' => false,
            ],
            self::CANVA_SIDE => [
                'font' => 'Poppins',
                'align' => 'left',
                'valign' => 'center',
                'uppercase' => false,
                'has_block' => true,
                'block_type' => 'side', // Ocupa metade da tela
            ],
            self::BOLD_CENTER => [
                'font' => 'Montserrat',
                'align' => 'center',
                'valign' => 'center',
                'uppercase' => true,
                'has_block' => false,
            ],
            self::MINIMAL => [
                'font' => 'Poppins',
                'align' => 'left',
                'valign' => 'bottom',
                'uppercase' => false,
                'has_block' => false,
            ],
            self::STACKED => [
                'font' => 'Montserrat',
                'align' => 'center',
                'valign' => 'center',
                'uppercase' => true,
                'has_block' => true,
                'block_type' => 'floating', // Bloco atrás do texto
            ],
        };
    }
}
