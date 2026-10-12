{{-- Pré-visualização de um alerta salvo (listagem): página inicial do site com o alerta, em escala --}}
@include('filament.alerts.styles')

<div
    class="al-modal-stage cx-ui"
    x-data="{
        scale: 1,
        measure() { this.scale = Math.min(1, ($el.clientWidth - 2) / 1280) },
    }"
    x-init="measure(); new ResizeObserver(() => measure()).observe($el)"
    x-bind:style="'height:' + Math.round(800 * scale) + 'px'"
>
    <div class="al-device-inner" x-bind:style="'transform:scale(' + scale + ')'">
        <iframe src="{{ route('alerts.preview', ['alerta' => $alert->getKey()]) }}" title="Pré-visualização de {{ $alert->name }}"></iframe>
    </div>
</div>
