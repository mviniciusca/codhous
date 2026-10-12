@props([
    'title',
    'description' => null,
    'icon' => null,
    'href' => null,
    'linkLabel' => 'Ver todos',
    'count' => null,
])

<section {{ $attributes->class('cx-panel') }} aria-labelledby="{{ $id = 'cx-'.\Illuminate\Support\Str::slug($title) }}">
    <header class="cx-panel-head">
        @if ($icon)
            <span class="cx-panel-icon" aria-hidden="true">
                <x-filament::icon :icon="$icon" class="cx-icon" />
            </span>
        @endif

        <div class="cx-panel-titles">
            <h2 id="{{ $id }}" class="cx-panel-title">
                {{ $title }}
                @if (filled($count))
                    <span class="cx-panel-count">{{ $count }}</span>
                @endif
            </h2>
            @if ($description)
                <p class="cx-panel-desc">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="cx-panel-actions">{{ $actions }}</div>
        @elseif ($href)
            <a href="{{ $href }}" class="cx-link">
                {{ $linkLabel }}
                <x-filament::icon icon="heroicon-m-arrow-right" class="cx-icon-sm" />
            </a>
        @endisset
    </header>

    {{ $slot }}
</section>
