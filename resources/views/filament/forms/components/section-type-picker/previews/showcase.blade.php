{{-- Galeria de obras: filtros e grade em mosaico --}}
<div class="stp-pv" style="gap: 7px">
    <div class="sk-row" style="--g: 4px">
        <span class="sk" style="width: 22px; height: 9px; border-radius: 999px; background: rgba(var(--primary-500), .55)"></span>
        <span class="sk" style="width: 28px; height: 9px; border-radius: 999px"></span>
        <span class="sk" style="width: 24px; height: 9px; border-radius: 999px"></span>
        <span class="sk" style="width: 30px; height: 9px; border-radius: 999px"></span>
    </div>
    <div style="flex: 1; display: grid; grid-template-columns: 1.3fr 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 5px">
        <div class="sk-img" style="grid-row: span 2">@include('filament.forms.components.section-type-picker.previews._img')</div>
        <div class="sk-img"></div>
        <div class="sk-img">@include('filament.forms.components.section-type-picker.previews._img')</div>
        <div class="sk-img" style="grid-column: span 2">@include('filament.forms.components.section-type-picker.previews._img')</div>
    </div>
</div>
