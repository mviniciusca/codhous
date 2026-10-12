{{-- Topbar: atalhos do Meu Workspace, tema (acesso rápido) e atalho para o site público --}}
@php $dueTasks = \App\Filament\Pages\Workspace::dueTaskCount(); @endphp
<nav class="cx-topbar-shortcuts" aria-label="Meu Workspace">
    @foreach ([
        'tarefas' => ['heroicon-m-check-circle', 'Minhas tarefas'],
        'agenda' => ['heroicon-m-calendar-days', 'Agenda'],
        'notas' => ['heroicon-m-document-text', 'Notas rápidas'],
    ] as $tab => [$icon, $label])
        @php $tooltip = $tab === 'tarefas' && $dueTasks ? $label.' · '.$dueTasks.($dueTasks === 1 ? ' vence hoje ou está atrasada' : ' vencem hoje ou estão atrasadas') : $label; @endphp
        <a
            href="{{ \App\Filament\Pages\Workspace::getUrl($tab === 'tarefas' ? [] : ['aba' => $tab]) }}"
            class="cx-topbar-icon"
            aria-label="{{ $tooltip }}"
            x-tooltip="{ content: @js($tooltip), theme: $store.theme }"
        >
            <x-filament::icon :icon="$icon" class="cx-icon-sm" aria-hidden="true" />
            @if ($tab === 'tarefas' && $dueTasks)
                <span class="cx-topbar-count" aria-hidden="true">{{ $dueTasks > 9 ? '9+' : $dueTasks }}</span>
            @endif
        </a>
    @endforeach
</nav>

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
