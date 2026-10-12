@props(['status'])

@php
    $label = \App\Filament\Resources\BudgetResource::STATUSES[$status] ?? $status;
    $tone = \App\Filament\Resources\BudgetResource::STATUS_COLORS[$status] ?? 'gray';
@endphp

<span {{ $attributes->class(['cx-badge', 'cx-tone-'.$tone]) }}>
    <span class="cx-dot" aria-hidden="true"></span>
    {{ $label }}
</span>
