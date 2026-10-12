{{-- Ilustração isométrica do formato da fórmula (laje, cilindro, peça retangular) --}}
@props(['shape' => 'custom'])

<svg {{ $attributes->merge(['class' => 'vc-shape']) }} viewBox="-34 0 294 170" role="img" aria-hidden="true" focusable="false">
    @switch($shape)
        @case('slab')
            <polygon class="vc-face-top" points="40,72 130,27 220,72 130,117" />
            <polygon class="vc-face-left" points="40,72 130,117 130,135 40,90" />
            <polygon class="vc-face-right" points="130,117 220,72 220,90 130,135" />
            <path class="vc-grid-line" d="M62.5 60.75 152.5 105.75 M85 49.5 175 94.5 M107.5 38.25 197.5 83.25 M62.5 83.25 152.5 38.25 M85 94.5 175 49.5 M107.5 105.75 197.5 60.75" />
            <path class="vc-dim" d="M36 98 126 143 M134 143 224 98 M30 72 30 90" />
            <text class="vc-dim-label" x="60" y="140" text-anchor="middle">Largura</text>
            <text class="vc-dim-label" x="204" y="140" text-anchor="middle">Comprimento</text>
            <text class="vc-dim-label" x="24" y="84" text-anchor="end">Espessura</text>
            @break

        @case('cylinder')
            <path class="vc-face-left" d="M95 34 V128 A35 11 0 0 0 165 128 V34 Z" />
            <path class="vc-face-right" d="M130 45 V139 A35 11 0 0 0 165 128 V34 A35 11 0 0 1 130 45 Z" />
            <ellipse class="vc-face-top" cx="130" cy="34" rx="35" ry="11" />
            <path class="vc-dim" d="M95 34 165 34 M182 34 182 128" />
            <text class="vc-dim-label" x="130" y="18" text-anchor="middle">Diâmetro</text>
            <text class="vc-dim-label" x="190" y="85">Altura</text>
            <text class="vc-dim-label vc-dim-note" x="130" y="162" text-anchor="middle">× quantidade de peças</text>
            @break

        @case('beam')
            <polygon class="vc-face-top" points="40,70 180,30 210,45 70,85" />
            <polygon class="vc-face-right" points="70,85 210,45 210,85 70,125" />
            <polygon class="vc-face-left" points="40,70 70,85 70,125 40,110" />
            <path class="vc-dim" d="M36 116 66 131 M76 133 216 93 M30 72 30 110" />
            <text class="vc-dim-label" x="44" y="140" text-anchor="middle">Largura</text>
            <text class="vc-dim-label" x="160" y="134" text-anchor="middle">Comprimento</text>
            <text class="vc-dim-label" x="24" y="94" text-anchor="end">Altura</text>
            <text class="vc-dim-label vc-dim-note" x="130" y="162" text-anchor="middle">× quantidade de peças</text>
            @break

        @default
            <rect class="vc-face-top" x="80" y="35" width="100" height="100" rx="22" />
            <text class="vc-formula-glyph" x="130" y="98" text-anchor="middle">ƒ(x)</text>
    @endswitch
</svg>
