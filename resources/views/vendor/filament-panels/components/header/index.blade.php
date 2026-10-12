{{--
    Cabeçalho de página compacto (uma linha) para todo o painel.
    Trilha com os níveis anteriores › título; subtítulo vira dica no ícone "?"; ações à direita.
    Padrão descrito em docs/PADRAO_UI_BUILDER.md (seção "Barra de título"). Estilos em filament/admin/theme.
--}}
@props([
    'actions' => [],
    'breadcrumbs' => [],
    'heading',
    'subheading' => null,
])

@php
    $subheadingText = filled($subheading) ? trim(preg_replace('/\s+/', ' ', strip_tags((string) $subheading))) : null;

    // Nas atualizações do Livewire a URL atual é /livewire/update: usa a da página original
    $normalizeUrl = fn (?string $url): string => rtrim((string) strtok((string) $url, '?#'), '/');
    $currentUrl = $normalizeUrl(\Livewire\Livewire::isLivewireRequest() ? \Livewire\Livewire::originalUrl() : url()->current());
    $sameText = fn ($a, $b): bool => mb_strtolower(trim(strip_tags((string) $a))) === mb_strtolower(trim(strip_tags((string) $b)));

    // O último nível é a própria página. Saem também os níveis que levam para esta mesma
    // página (listagens) ou que repetem o título
    $crumbs = collect($breadcrumbs)
        ->map(fn ($label, $url) => ['url' => is_string($url) ? $url : null, 'label' => $label])
        ->values()
        ->slice(0, -1)
        ->reject(fn (array $crumb): bool => ($crumb['url'] && $normalizeUrl($crumb['url']) === $currentUrl) || $sameText($crumb['label'], $heading));
@endphp

<header
    {{ $attributes->class(['fi-header cx-pagebar']) }}
    x-data
>
    <div class="cx-pagebar-start">
        @foreach ($crumbs as $crumb)
            @if ($crumb['url'])
                <a href="{{ $crumb['url'] }}" wire:navigate class="cx-pagebar-crumb">{{ $crumb['label'] }}</a>
            @else
                <span class="cx-pagebar-crumb">{{ $crumb['label'] }}</span>
            @endif
            <x-filament::icon icon="heroicon-m-chevron-right" class="cx-pagebar-sep" />
        @endforeach

        <h1 class="fi-header-heading cx-pagebar-title">{{ $heading }}</h1>

        @if ($subheadingText)
            <span
                class="fi-header-subheading cx-pagebar-hint"
                tabindex="0"
                x-tooltip="{ content: @js($subheadingText), theme: $store.theme, placement: 'bottom-start', maxWidth: 360 }"
            >
                <x-filament::icon icon="heroicon-m-question-mark-circle" />
                <span class="sr-only">{{ $subheadingText }}</span>
            </span>
        @endif
    </div>

    <div class="cx-pagebar-actions">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $this->getRenderHookScopes()) }}

        @if ($actions)
            <x-filament::actions :actions="$actions" />
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>
</header>
