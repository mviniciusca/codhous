<?php

namespace App\Enums;

enum CardPreset: string
{
    case TOP_LEFT = 'top_left';         // Home Quarantine Playlist
    case TOP_CENTER = 'top_center';     // Quarantine and Chill
    case TOP_RIGHT = 'top_right';       // Stay Home Enjoy Music
    case BOTTOM_LEFT = 'bottom_left';   // Stay Home and Enjoy Your Music
    case BOTTOM_CENTER = 'bottom_center'; // Stay Entertained
    case BOTTOM_RIGHT = 'bottom_right'; // Stay Home (with yellow bg)

    public function label(): string
    {
        return match($this) {
            self::TOP_LEFT => 'Playlist Style',
            self::TOP_CENTER => 'Chill Split',
            self::TOP_RIGHT => 'Modern Mix',
            self::BOTTOM_LEFT => 'Geometric L',
            self::BOTTOM_CENTER => 'Horizontal Split',
            self::BOTTOM_RIGHT => 'Solid Impact',
        };
    }

    /**
     * Retorna a descrição do layout para o preview ou logs.
     */
    public function description(): string
    {
        return match($this) {
            self::TOP_LEFT => 'Imagem central com borda espessa e blocos de cor nos cantos.',
            self::TOP_CENTER => 'Divisão 50/50 com grafismos de listras e pontos.',
            self::TOP_RIGHT => 'Foto à direita com mix de tipografia sólida e outline.',
            self::BOTTOM_LEFT => 'Fundo predominante com moldura em L e texto à direita.',
            self::BOTTOM_CENTER => 'Divisão horizontal, texto em duas camadas e numeração vertical.',
            self::BOTTOM_RIGHT => 'Fundo sólido com círculos concêntricos e blocos de rodapé.',
        };
    }
}
