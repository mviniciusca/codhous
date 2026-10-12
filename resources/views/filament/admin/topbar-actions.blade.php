{{-- Topbar: atalho para o site público --}}
<a href="{{ url('/') }}" target="_blank" rel="noopener" class="cx-topbar-btn" x-tooltip="{ content: 'Abrir o site em nova aba', theme: $store.theme }">
    <x-filament::icon icon="heroicon-m-globe-alt" class="cx-icon-sm" aria-hidden="true" />
    <span>Ver site</span>
</a>
