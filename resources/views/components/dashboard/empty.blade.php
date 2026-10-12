@props([
    'icon' => 'heroicon-o-inbox',
    'title',
    'description' => null,
    'tone' => 'gray',
])

<div {{ $attributes->class(['cx-empty', 'cx-tone-'.$tone]) }}>
    <span class="cx-empty-icon" aria-hidden="true">
        <x-filament::icon :icon="$icon" class="cx-icon" />
    </span>
    <p class="cx-empty-title">{{ $title }}</p>
    @if ($description)
        <p class="cx-empty-desc">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
