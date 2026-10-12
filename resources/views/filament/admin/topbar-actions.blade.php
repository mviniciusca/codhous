{{-- Topbar: tema (acesso rápido) e atalho para o site público --}}
<x-filament::dropdown placement="bottom-end" teleport width="xs" class="cx-topbar-theme">
    <x-slot name="trigger">
        <button
            type="button"
            class="cx-topbar-icon"
            aria-label="Aparência"
            x-data="{ theme: localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value) }"
            x-on:theme-changed.window="theme = $event.detail"
            x-tooltip="{ content: 'Aparência', theme: $store.theme }"
        >
            <x-filament::icon icon="heroicon-m-sun" class="cx-icon-sm" x-show="theme === 'light'" />
            <x-filament::icon icon="heroicon-m-moon" class="cx-icon-sm" x-show="theme === 'dark'" x-cloak />
            <x-filament::icon icon="heroicon-m-computer-desktop" class="cx-icon-sm" x-show="theme === 'system'" x-cloak />
        </button>
    </x-slot>
    <div class="cx-um-section cx-um-section-solo">
        <span class="cx-um-label" id="cx-topbar-theme">Aparência</span>
        @include('filament.admin.theme-options', ['labelledBy' => 'cx-topbar-theme'])
    </div>
</x-filament::dropdown>

<a href="{{ url('/') }}" target="_blank" rel="noopener" class="cx-topbar-btn" x-tooltip="{ content: 'Abrir o site em nova aba', theme: $store.theme }">
    <x-filament::icon icon="heroicon-m-globe-alt" class="cx-icon-sm" aria-hidden="true" />
    <span>Ver site</span>
</a>
