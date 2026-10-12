@props([
    'label',
    'value',
    'note' => null,
    'tone' => 'gray',
    'url' => null,
])

<a href="{{ $url }}" {{ $attributes->class(['cx-kpi', 'cx-tone-'.$tone]) }}>
    <span class="cx-kpi-label">
        <span class="cx-dot" aria-hidden="true"></span>
        {{ $label }}
        <x-filament::icon icon="heroicon-m-arrow-up-right" class="cx-kpi-go" aria-hidden="true" />
    </span>

    <span class="cx-kpi-value">{{ $value }}</span>

    @if ($note)
        <span class="cx-kpi-note">{{ $note }}</span>
    @endif

    {{ $slot }}
</a>
