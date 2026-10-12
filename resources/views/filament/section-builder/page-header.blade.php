{{-- Cabeçalho compacto do construtor de seções: trilha, título e ações numa linha só --}}
@php
    $breadcrumbs = filament()->hasBreadcrumbs() ? $breadcrumbs : [];
@endphp

<header class="sb-pagebar">
    <div class="sb-pagebar-start">
        @foreach ($breadcrumbs as $url => $label)
            @if (is_string($url) && ! $loop->last)
                <a href="{{ $url }}" wire:navigate class="sb-pagebar-crumb">{{ $label }}</a>
                <x-filament::icon icon="heroicon-m-chevron-right" class="sb-pagebar-sep" />
            @endif
        @endforeach

        <h1 class="sb-pagebar-title">{{ $heading }}</h1>

        @if (filled($subheading))
            <span class="sb-pagebar-hint" x-tooltip="{ content: @js($subheading), theme: $store.theme }" aria-label="{{ $subheading }}">
                <x-filament::icon icon="heroicon-m-question-mark-circle" />
            </span>
        @endif
    </div>

    @if (count($actions))
        <div class="sb-pagebar-actions">
            @foreach ($actions as $action)
                @if ($action->isVisible())
                    {{ $action->size('sm') }}
                @endif
            @endforeach
        </div>
    @endif
</header>

<style>
    /* Aproxima a barra do topo e do construtor (a página usa py-8 e gap-y-8) */
    .sb-pagebar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 36px;
        margin: -20px 0 -18px;
    }
    .sb-pagebar-start { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .sb-pagebar-crumb { flex: none; font-size: 14px; font-weight: 500; color: rgb(var(--gray-500)); transition: color .15s; }
    .sb-pagebar-crumb:hover { color: rgb(var(--gray-950)); }
    .dark .sb-pagebar-crumb { color: rgb(var(--gray-400)); }
    .dark .sb-pagebar-crumb:hover { color: #fff; }
    .sb-pagebar-sep { width: 16px; height: 16px; flex: none; color: rgb(var(--gray-400)); }
    .dark .sb-pagebar-sep { color: rgb(var(--gray-600)); }
    .sb-pagebar-title {
        overflow: hidden;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.01em;
        white-space: nowrap;
        text-overflow: ellipsis;
        color: rgb(var(--gray-950));
    }
    .dark .sb-pagebar-title { color: #fff; }
    .sb-pagebar-hint { display: grid; place-items: center; flex: none; color: rgb(var(--gray-400)); cursor: help; }
    .sb-pagebar-hint svg { width: 16px; height: 16px; }
    .sb-pagebar-actions { display: flex; align-items: center; gap: 8px; flex: none; }
</style>
