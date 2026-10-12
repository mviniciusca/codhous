@php
    $i = fn (string $name, string $class = 'h-5 w-5') => svg('heroicon-o-'.$name, $class, ['aria-hidden' => 'true'])->toHtml();
@endphp

<x-filament-panels::page>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ \App\Services\PosterStudio\Fonts::googleUrl() }}">
    @vite('resources/js/poster-studio/index.js')

    <section class="psg" wire:ignore x-data="{ format: 'instagram_square', custom: { width: 1080, height: 1080 }, creating: null, limits: @js($customLimits) }">
        <header class="psg-head">
            <div>
                <h2>Criar nova arte</h2>
                <p>Escolha o formato e um template. Logo, telefone, WhatsApp e fotos da empresa entram automaticamente.</p>
            </div>
        </header>

        <div class="psg-formats" role="radiogroup" aria-label="Formato do post">
            @foreach ($formats as $key => $format)
                <button type="button" role="radio" class="psg-format" :aria-checked="format === '{{ $key }}'" @click="format = '{{ $key }}'">
                    <span class="psg-ratio" style="aspect-ratio: {{ $format['width'] }} / {{ $format['height'] }}"></span>
                    <span><b>{{ $format['label'] }}</b><small>{{ $format['width'] }}×{{ $format['height'] }}</small></span>
                </button>
            @endforeach
            <div class="psg-format" :aria-checked="format === 'custom'" role="radio" @click="format = 'custom'" style="cursor: pointer">
                <span class="psg-ratio" :style="{ aspectRatio: (custom.width || 1) + ' / ' + (custom.height || 1) }"></span>
                <span>
                    <b>Personalizado</b>
                    <span class="psg-custom">
                        <input type="number" x-model.number="custom.width" :min="limits.min" :max="limits.max" aria-label="Largura" @focus="format = 'custom'">
                        ×
                        <input type="number" x-model.number="custom.height" :min="limits.min" :max="limits.max" aria-label="Altura" @focus="format = 'custom'">
                    </span>
                </span>
            </div>
        </div>

        <div class="psg-templates">
            @foreach ($templates as $template)
                <button type="button" class="psg-template" :disabled="creating !== null"
                        @click="creating = '{{ $template['id'] }}'; $wire.createDesign('{{ $template['id'] }}', format, format === 'custom' ? custom.width : null, format === 'custom' ? custom.height : null).finally(() => creating = null)">
                    <span class="psg-thumb" x-data="posterPreview(@js($template['document']), 300)">
                        <template x-if="src"><img :src="src" alt="Prévia do template {{ $template['name'] }}"></template>
                        <template x-if="!src && !failed"><span class="psg-skeleton"></span></template>
                        <span class="psg-loading" x-show="creating === '{{ $template['id'] }}'">Criando…</span>
                    </span>
                    <b>{{ $template['name'] }}</b>
                    <small>{{ $template['category'] }}</small>
                </button>
            @endforeach
        </div>
    </section>

    <div>
        <h2 class="psg-subtitle">Suas artes</h2>
        {{ $this->table }}
    </div>

    <style>
        .psg { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,.05); outline: 1px solid rgba(3,7,18,.05); }
        .dark .psg { background: rgb(24 24 27); outline-color: rgba(255,255,255,.1); }
        .psg-head h2, .psg-subtitle { font-size: 16px; font-weight: 600; margin: 0; }
        .psg-subtitle { margin-bottom: 12px; }
        .psg-head p { margin: 4px 0 0; font-size: 13px; color: rgb(107 114 128); }
        .psg-formats { display: flex; gap: 8px; overflow-x: auto; padding: 16px 0 4px; }
        .psg-format { display: flex; align-items: center; gap: 10px; flex: none; padding: 8px 12px 8px 10px; border-radius: 10px; border: 1px solid rgb(229 231 235); background: transparent; text-align: left; font-size: 12px; color: inherit; }
        .dark .psg-format { border-color: rgba(255,255,255,.1); }
        .psg-format b { display: block; font-weight: 600; }
        .psg-format small { color: rgb(107 114 128); }
        .psg-format[aria-checked="true"] { border-color: #1769ff; box-shadow: 0 0 0 1px #1769ff; background: rgba(23,105,255,.06); }
        .psg-ratio { width: 22px; max-height: 32px; border-radius: 3px; border: 2px solid currentColor; opacity: .55; flex: none; }
        .psg-custom { display: flex; align-items: center; gap: 4px; color: rgb(107 114 128); }
        .psg-custom input { width: 62px; padding: 2px 4px; border-radius: 6px; border: 1px solid rgb(209 213 219); background: transparent; font-size: 12px; color: inherit; }
        .psg-templates { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; margin-top: 16px; }
        .psg-template { display: flex; flex-direction: column; gap: 4px; padding: 0; border: 0; background: none; text-align: left; color: inherit; }
        .psg-template:disabled { cursor: wait; }
        .psg-template b { font-size: 13px; font-weight: 600; }
        .psg-template small { font-size: 12px; color: rgb(107 114 128); }
        .psg-thumb { position: relative; display: block; aspect-ratio: 1; border-radius: 10px; overflow: hidden; background: rgb(243 244 246); outline: 1px solid rgba(3,7,18,.06); transition: transform .15s, box-shadow .15s; margin-bottom: 4px; }
        .dark .psg-thumb { background: rgba(255,255,255,.05); }
        .psg-template:hover:not(:disabled) .psg-thumb { transform: translateY(-2px); box-shadow: 0 10px 24px -10px rgba(0,0,0,.35); outline-color: #1769ff; }
        .psg-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .psg-skeleton { position: absolute; inset: 0; background: linear-gradient(100deg, transparent 30%, rgba(255,255,255,.6) 50%, transparent 70%) rgb(243 244 246); background-size: 200% 100%; animation: psg-shimmer 1.3s infinite; }
        .dark .psg-skeleton { background-color: rgba(255,255,255,.05); }
        .psg-loading { position: absolute; inset: 0; display: grid; place-items: center; background: rgba(17,24,39,.55); color: #fff; font-weight: 600; font-size: 13px; }
        @keyframes psg-shimmer { to { background-position: -200% 0; } }
    </style>
</x-filament-panels::page>
