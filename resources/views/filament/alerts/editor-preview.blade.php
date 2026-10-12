{{--
    Prévia ao vivo do editor de alertas. O iframe carrega uma página real do site uma única vez
    (alerts.preview); a cada alteração, renderAlertPreview() devolve o HTML do componente do site
    e ele é enviado ao iframe (filament/alerts/preview-bridge). Estilos: filament/alerts/styles.
--}}
@include('filament.alerts.styles')
@include('filament.alerts.editor-script')

<section
    class="al-panel al-preview cx-ui"
    aria-labelledby="al-preview-title"
    x-data="alertEditorPreview({ src: @js(route('alerts.preview')) })"
    x-bind:class="{ 'is-expanded': expanded }"
    x-on:keydown.escape.window="expanded && toggleExpanded()"
    wire:ignore
>
    <header class="al-panel-head">
        <span class="al-panel-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-eye" /></span>
        <div class="al-panel-text">
            <h2 id="al-preview-title" class="al-panel-title">Pré-visualização ao vivo</h2>
            <p class="al-panel-desc">Como o alerta aparece no site. Atualiza enquanto você edita.</p>
        </div>

        <div class="al-seg" role="radiogroup" aria-label="Dispositivo">
            @foreach (['desktop' => ['heroicon-m-computer-desktop', 'Desktop'], 'mobile' => ['heroicon-m-device-phone-mobile', 'Celular']] as $device => [$icon, $label])
                <button
                    type="button"
                    role="radio"
                    class="al-seg-btn"
                    x-bind:class="{ 'is-active': device === @js($device) }"
                    x-bind:aria-checked="(device === @js($device)).toString()"
                    x-on:click="setDevice(@js($device))"
                >
                    <x-filament::icon :icon="$icon" />
                    <span class="al-seg-label">{{ $label }}</span>
                </button>
            @endforeach
        </div>

        <button
            type="button"
            class="al-icon-btn"
            x-on:click="toggleExpanded()"
            x-bind:aria-label="expanded ? 'Recolher prévia' : 'Expandir prévia'"
            x-tooltip="{ content: expanded ? 'Recolher (Esc)' : 'Expandir prévia', theme: $store.theme }"
        >
            <x-filament::icon icon="heroicon-m-arrows-pointing-out" x-show="! expanded" />
            <x-filament::icon icon="heroicon-m-arrows-pointing-in" x-show="expanded" x-cloak />
        </button>
    </header>

    <div class="al-stage" x-ref="stage" x-bind:class="'is-' + device" x-bind:style="stageStyle">
        <div class="al-device" x-bind:style="outerStyle">
            <div class="al-device-inner" x-bind:style="innerStyle">
                <iframe x-ref="frame" x-bind:src="frameSrc" title="Pré-visualização do alerta no site" tabindex="-1"></iframe>
            </div>
        </div>

        <div class="al-stage-loading" x-show="! ready" x-cloak>
            <x-filament::loading-indicator class="cx-icon" />
            <span>Carregando o site…</span>
        </div>
    </div>

    <footer class="al-preview-foot">
        <span class="al-preview-status" x-bind:data-status="status">
            <span class="al-dot" aria-hidden="true"></span>
            <span x-text="statusLabel"></span>
        </span>
        <span class="al-preview-hint" x-show="hint" x-text="hint" x-cloak></span>
        <button type="button" class="al-text-btn" x-on:click="push()" x-bind:disabled="! ready">
            <x-filament::icon icon="heroicon-m-arrow-path" class="cx-icon-xs" />
            Mostrar de novo
        </button>
    </footer>
</section>
