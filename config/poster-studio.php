<?php

/*
|--------------------------------------------------------------------------
| Estúdio de Posts
|--------------------------------------------------------------------------
|
| Fonte única dos formatos de publicação, paletas e fontes usados pelo
| editor de artes (app/Services/PosterStudio). O frontend recebe estes
| valores prontos; não duplique dimensões ou cores em JS/Blade.
|
*/

return [

    'schema_version' => 1,

    'default_format' => 'instagram_square',

    'default_palette' => 'brand',

    /*
     | Formatos de publicação. "group" agrupa formatos com a mesma proporção
     | para que os templates possam definir um layout por proporção.
     */
    'formats' => [
        'instagram_square' => ['label' => 'Instagram quadrado', 'platform' => 'Instagram', 'icon' => 'instagram', 'width' => 1080, 'height' => 1080, 'group' => 'square'],
        'instagram_portrait' => ['label' => 'Instagram vertical', 'platform' => 'Instagram', 'icon' => 'instagram', 'width' => 1080, 'height' => 1350, 'group' => 'portrait'],
        'story' => ['label' => 'Stories / Reels', 'platform' => 'Instagram', 'icon' => 'smartphone', 'width' => 1080, 'height' => 1920, 'group' => 'story'],
        'whatsapp_status' => ['label' => 'Status do WhatsApp', 'platform' => 'WhatsApp', 'icon' => 'message-circle', 'width' => 1080, 'height' => 1920, 'group' => 'story'],
        'facebook_square' => ['label' => 'Facebook quadrado', 'platform' => 'Facebook', 'icon' => 'facebook', 'width' => 1080, 'height' => 1080, 'group' => 'square'],
        'landscape' => ['label' => 'Post horizontal', 'platform' => 'Facebook / LinkedIn', 'icon' => 'monitor', 'width' => 1200, 'height' => 628, 'group' => 'landscape'],
    ],

    'custom_format' => [
        'min' => 200,
        'max' => 4000,
    ],

    /*
     | Papéis semânticos de cor. Os elementos guardam o papel (ex.: {"role":"primary"}),
     | então trocar a paleta recolore a arte inteira sem tocar em textos e fotos.
     */
    'color_roles' => [
        'primary' => 'Principal',
        'secondary' => 'Secundária',
        'accent' => 'Destaque',
        'background' => 'Fundo',
        'surface' => 'Superfície',
        'text' => 'Texto',
        'muted' => 'Texto secundário',
        'onPrimary' => 'Texto sobre principal',
        'dark' => 'Escuro',
        'onDark' => 'Texto sobre escuro',
    ],

    'palettes' => [
        'laranja' => ['name' => 'Laranja', 'description' => 'Industrial', 'colors' => [
            'primary' => '#FF5A1F', 'secondary' => '#FF8A3D', 'accent' => '#FF3D00', 'background' => '#FFFFFF', 'surface' => '#FFF4ED',
            'text' => '#111827', 'muted' => '#4B5563', 'onPrimary' => '#FFFFFF', 'dark' => '#111827', 'onDark' => '#FFFFFF',
        ]],
        'azul' => ['name' => 'Azul', 'description' => 'Corporativo', 'colors' => [
            'primary' => '#1769FF', 'secondary' => '#53A0FF', 'accent' => '#0047AB', 'background' => '#FFFFFF', 'surface' => '#EEF4FF',
            'text' => '#0F172A', 'muted' => '#475569', 'onPrimary' => '#FFFFFF', 'dark' => '#0B1B3F', 'onDark' => '#FFFFFF',
        ]],
        'verde' => ['name' => 'Verde', 'description' => 'Sustentável', 'colors' => [
            'primary' => '#159447', 'secondary' => '#63C174', 'accent' => '#087443', 'background' => '#FFFFFF', 'surface' => '#ECFDF3',
            'text' => '#0F1F17', 'muted' => '#4B5B52', 'onPrimary' => '#FFFFFF', 'dark' => '#0B2918', 'onDark' => '#FFFFFF',
        ]],
        'vermelho' => ['name' => 'Vermelho', 'description' => 'Impacto', 'colors' => [
            'primary' => '#E11D2E', 'secondary' => '#F25563', 'accent' => '#B00020', 'background' => '#FFFFFF', 'surface' => '#FEF2F2',
            'text' => '#1A1A1A', 'muted' => '#52525B', 'onPrimary' => '#FFFFFF', 'dark' => '#1A0A0C', 'onDark' => '#FFFFFF',
        ]],
        'roxo' => ['name' => 'Roxo', 'description' => 'Moderno', 'colors' => [
            'primary' => '#7C3AED', 'secondary' => '#A78BFA', 'accent' => '#5B21B6', 'background' => '#FFFFFF', 'surface' => '#F5F3FF',
            'text' => '#1E1B2E', 'muted' => '#5B5670', 'onPrimary' => '#FFFFFF', 'dark' => '#1E1033', 'onDark' => '#FFFFFF',
        ]],
        'preto' => ['name' => 'Preto', 'description' => 'Premium', 'colors' => [
            'primary' => '#111111', 'secondary' => '#3F3F46', 'accent' => '#D4A017', 'background' => '#FAFAF9', 'surface' => '#F4F4F5',
            'text' => '#0A0A0A', 'muted' => '#52525B', 'onPrimary' => '#FFFFFF', 'dark' => '#0A0A0A', 'onDark' => '#F5F5F4',
        ]],
        'azul_laranja' => ['name' => 'Azul e laranja', 'description' => 'Contraste', 'colors' => [
            'primary' => '#1D4ED8', 'secondary' => '#3B82F6', 'accent' => '#F97316', 'background' => '#FFFFFF', 'surface' => '#EFF6FF',
            'text' => '#0F172A', 'muted' => '#475569', 'onPrimary' => '#FFFFFF', 'dark' => '#0F172A', 'onDark' => '#FFFFFF',
        ]],
        'laranja_noturno' => ['name' => 'Laranja noturno', 'description' => 'Versão escura', 'colors' => [
            'primary' => '#FF5A1F', 'secondary' => '#FF8A3D', 'accent' => '#FFB020', 'background' => '#0F0F10', 'surface' => '#1A1A1D',
            'text' => '#FFFFFF', 'muted' => '#A1A1AA', 'onPrimary' => '#FFFFFF', 'dark' => '#000000', 'onDark' => '#FFFFFF',
        ]],
        'verde_preto' => ['name' => 'Verde e preto', 'description' => 'Versão escura', 'colors' => [
            'primary' => '#22C55E', 'secondary' => '#86EFAC', 'accent' => '#16A34A', 'background' => '#111111', 'surface' => '#1C1C1C',
            'text' => '#F9FAFB', 'muted' => '#A1A1AA', 'onPrimary' => '#0A0A0A', 'dark' => '#000000', 'onDark' => '#FFFFFF',
        ]],
        'vermelho_branco' => ['name' => 'Vermelho e branco', 'description' => 'Clássico', 'colors' => [
            'primary' => '#DC2626', 'secondary' => '#FCA5A5', 'accent' => '#991B1B', 'background' => '#FFFFFF', 'surface' => '#FFF5F5',
            'text' => '#111111', 'muted' => '#57534E', 'onPrimary' => '#FFFFFF', 'dark' => '#7F1D1D', 'onDark' => '#FFFFFF',
        ]],
        'monocromatico' => ['name' => 'Monocromático', 'description' => 'Tons de cinza', 'colors' => [
            'primary' => '#262626', 'secondary' => '#525252', 'accent' => '#737373', 'background' => '#FFFFFF', 'surface' => '#F5F5F5',
            'text' => '#0A0A0A', 'muted' => '#525252', 'onPrimary' => '#FFFFFF', 'dark' => '#171717', 'onDark' => '#FFFFFF',
        ]],
        'neutro' => ['name' => 'Neutro', 'description' => 'Minimalista', 'colors' => [
            'primary' => '#57534E', 'secondary' => '#A8A29E', 'accent' => '#78716C', 'background' => '#FAF7F2', 'surface' => '#F0EBE3',
            'text' => '#1C1917', 'muted' => '#78716C', 'onPrimary' => '#FFFFFF', 'dark' => '#292524', 'onDark' => '#FAF7F2',
        ]],
    ],

    /*
     | Fontes carregadas do Google Fonts no editor. A exportação aguarda o
     | carregamento delas antes de renderizar.
     */
    'fonts' => [
        'Montserrat' => ['weights' => [400, 600, 700, 800, 900], 'italic' => true],
        'Inter' => ['weights' => [400, 500, 600, 700, 800], 'italic' => true],
        'Poppins' => ['weights' => [400, 600, 700, 800], 'italic' => true],
        'Oswald' => ['weights' => [400, 500, 600, 700], 'italic' => false],
        'Bebas Neue' => ['weights' => [400], 'italic' => false],
        'Anton' => ['weights' => [400], 'italic' => false],
        'Archivo Black' => ['weights' => [400], 'italic' => false],
        'Playfair Display' => ['weights' => [400, 700, 800], 'italic' => true],
        'Roboto Condensed' => ['weights' => [400, 700], 'italic' => true],
        'Jost' => ['weights' => [400, 500, 700], 'italic' => true],
    ],

    'uploads' => [
        'disk' => 'public',
        'directory' => 'poster-studio/assets',
        'max_kb' => 8192,
        'mimes' => ['png', 'jpg', 'jpeg', 'webp'],
        'max_dimension' => 6000,
    ],

    'thumbnails' => [
        'directory' => 'poster-studio/thumbnails',
        'max_kb' => 600,
    ],

    'limits' => [
        'elements' => 150,
        'text_length' => 2000,
    ],
];
