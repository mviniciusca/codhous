{{-- Live preview da seção (coluna central do construtor). Lógica em filament/section-builder/script --}}
@php
    /** @var array<string, array<string, mixed>> $types */
    $previewsPath = 'filament.forms.components.section-type-picker.previews';
@endphp

@include('filament.section-builder.script')
@include('filament.forms.components.section-type-picker.styles')

<section
    class="sb-canvas"
    aria-label="Pré-visualização da seção"
    x-data="sectionBuilderPreview({ type: $wire.$entangle(@js($typeStatePath)) })"
    x-bind:class="{ 'is-expanded': expanded }"
    x-on:keydown.escape.window="expanded && toggleExpanded()"
    wire:ignore
>
    <div class="sb-toolbar">
        <div class="sb-toolbar-group">
            <div class="sb-seg" role="radiogroup" aria-label="Modo de visualização">
                <button type="button" role="radio" class="sb-seg-btn" x-bind:class="{ 'is-active': mode === 'live' }" x-bind:aria-checked="mode === 'live'" x-on:click="setMode('live')">
                    <x-filament::icon icon="heroicon-m-eye" />
                    <span>Ao vivo</span>
                </button>
                <button type="button" role="radio" class="sb-seg-btn" x-bind:class="{ 'is-active': mode === 'structure' }" x-bind:aria-checked="mode === 'structure'" x-on:click="setMode('structure')">
                    <x-filament::icon icon="heroicon-m-squares-2x2" />
                    <span>Estrutura</span>
                </button>
            </div>

            <span class="sb-status" x-bind:data-status="status" x-show="type && mode === 'live'" x-cloak>
                <span class="sb-status-dot"></span>
                <span x-text="statusLabel"></span>
            </span>
        </div>

        <div class="sb-seg" role="radiogroup" aria-label="Dispositivo" x-bind:class="{ 'is-disabled': mode !== 'live' }">
            @foreach (['desktop' => ['heroicon-o-computer-desktop', 'Desktop'], 'tablet' => ['heroicon-o-device-tablet', 'Tablet'], 'mobile' => ['heroicon-o-device-phone-mobile', 'Celular']] as $device => [$icon, $label])
                <button
                    type="button"
                    role="radio"
                    class="sb-seg-btn sb-seg-btn--icon"
                    x-bind:class="{ 'is-active': device === @js($device) }"
                    x-bind:aria-checked="device === @js($device)"
                    x-on:click="setDevice(@js($device))"
                    x-tooltip="{ content: @js($label), theme: $store.theme }"
                    aria-label="{{ $label }}"
                >
                    <x-filament::icon :icon="$icon" />
                </button>
            @endforeach
        </div>

        <div class="sb-toolbar-group sb-toolbar-group--end">
            <div class="sb-zoom" x-show="mode === 'live'">
                <button type="button" class="sb-icon-btn" x-on:click="zoomBy(-.1)" aria-label="Diminuir zoom"><x-filament::icon icon="heroicon-m-minus" /></button>
                <button type="button" class="sb-zoom-value" x-on:click="zoom = null; $refs.viewport.classList.remove('is-zoomed'); measure()" x-text="Math.round(scale * 100) + '%'" x-tooltip="{ content: 'Ajustar à tela', theme: $store.theme }"></button>
                <button type="button" class="sb-icon-btn" x-on:click="zoomBy(.1)" aria-label="Aumentar zoom"><x-filament::icon icon="heroicon-m-plus" /></button>
            </div>

            <button
                type="button"
                class="sb-icon-btn"
                x-show="mode === 'live'"
                x-bind:class="{ 'is-on': chrome }"
                x-bind:aria-pressed="chrome"
                x-on:click="toggleChrome()"
                x-tooltip="{ content: chrome ? 'Ocultar cabeçalho e rodapé do site' : 'Ver no contexto do site (cabeçalho e rodapé)', theme: $store.theme }"
                aria-label="Contexto do site"
            >
                <x-filament::icon icon="heroicon-o-window" />
            </button>
            <button type="button" class="sb-icon-btn" x-show="mode === 'live'" x-on:click="refresh()" x-tooltip="{ content: 'Atualizar prévia', theme: $store.theme }" aria-label="Atualizar prévia">
                <x-filament::icon icon="heroicon-m-arrow-path" x-bind:class="{ 'sb-spin': status === 'loading' }" />
            </button>
            <a class="sb-icon-btn" x-show="mode === 'live' && url" x-bind:href="url" target="_blank" rel="noopener" x-tooltip="{ content: 'Abrir em nova aba', theme: $store.theme }" aria-label="Abrir em nova aba">
                <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" />
            </a>
            <button type="button" class="sb-icon-btn" x-on:click="toggleExpanded()" x-tooltip="{ content: expanded ? 'Recolher (Esc)' : 'Expandir prévia', theme: $store.theme }" x-bind:aria-label="expanded ? 'Recolher prévia' : 'Expandir prévia'">
                <x-filament::icon icon="heroicon-m-arrows-pointing-out" x-show="! expanded" />
                <x-filament::icon icon="heroicon-m-arrows-pointing-in" x-show="expanded" x-cloak />
            </button>
        </div>
    </div>

    <div class="sb-viewport" x-ref="viewport">
        {{-- Ao vivo: a seção renderizada pelo próprio site, com o estado não salvo --}}
        <div class="sb-device-frame" x-bind:class="'is-' + device" x-bind:style="frameOuterStyle" x-show="type && mode === 'live'">
            <div class="sb-device-inner" x-bind:style="frameInnerStyle">
                <iframe x-ref="frameA" title="Pré-visualização" class="sb-frame" x-bind:class="{ 'is-active': activeFrame === 0 }" tabindex="-1"></iframe>
                <iframe x-ref="frameB" title="Pré-visualização" class="sb-frame" x-bind:class="{ 'is-active': activeFrame === 1 }" tabindex="-1"></iframe>
            </div>
            <div class="sb-loading-bar" x-show="status === 'loading'" x-cloak></div>
        </div>

        {{-- Estrutura: skeleton ampliado do componente (também quando ainda não há conteúdo) --}}
        <div class="sb-skeleton" x-show="type && showSkeleton" x-cloak x-transition.opacity.duration.150ms>
            <div class="sb-skeleton-stage stp-tokens" x-ref="stage" x-bind:style="'--k:' + skeletonScale">
                @foreach ($types as $key => $type)
                    <div class="stp-thumb sb-skeleton-thumb" x-show="type === @js($key)" aria-hidden="true">
                        @includeIf("{$previewsPath}.{$key}")
                    </div>
                @endforeach
            </div>

            <p class="sb-skeleton-note" x-show="mode === 'live' && rendered.empty" x-cloak>
                <x-filament::icon icon="heroicon-m-information-circle" />
                <span x-text="rendered.error ? 'Complete os campos obrigatórios para ver a seção real.' : 'A seção ainda não tem conteúdo. Mostrando a estrutura do componente.'"></span>
            </p>
        </div>

        {{-- Nenhum componente escolhido --}}
        <div class="sb-empty" x-show="! type" x-cloak>
            <span class="sb-empty-icon"><x-filament::icon icon="heroicon-o-cursor-arrow-rays" /></span>
            <p class="sb-empty-title">Escolha um componente</p>
            <p class="sb-empty-desc">Selecione um tipo de seção na biblioteca para ver a prévia aqui. A prévia é atualizada enquanto você edita.</p>
        </div>

        <div class="sb-viewport-error" x-show="type && mode === 'live' && status === 'error'" x-cloak>
            <x-filament::icon icon="heroicon-m-exclamation-triangle" />
            <span>Não foi possível atualizar a prévia.</span>
            <button type="button" x-on:click="refresh()">Tentar novamente</button>
        </div>
    </div>
</section>
