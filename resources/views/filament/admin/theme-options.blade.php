{{-- Escolha de tema (Claro / Escuro / Sistema). Mesmo mecanismo do theme-switcher do Filament --}}
<div
    x-data="{ theme: null }"
    x-init="
        theme = localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value);
        $watch('theme', () => $dispatch('theme-changed', theme));
    "
    x-on:theme-changed.window="theme = $event.detail"
    class="cx-theme"
    role="radiogroup"
    @isset($labelledBy) aria-labelledby="{{ $labelledBy }}" @else aria-label="Aparência" @endisset
>
    @foreach (['light' => ['heroicon-m-sun', 'Claro'], 'dark' => ['heroicon-m-moon', 'Escuro'], 'system' => ['heroicon-m-computer-desktop', 'Sistema']] as $value => [$icon, $label])
        <button
            type="button"
            role="radio"
            x-on:click="theme = '{{ $value }}'"
            x-bind:aria-checked="theme === '{{ $value }}'"
            class="cx-theme-opt"
        >
            <x-filament::icon :icon="$icon" class="cx-icon-sm" aria-hidden="true" />
            <span>{{ $label }}</span>
        </button>
    @endforeach
</div>
