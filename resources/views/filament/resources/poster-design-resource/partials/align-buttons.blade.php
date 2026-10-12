{{-- Alinhamento: ao canvas (1 elemento) ou entre si (vários). Ícones desenhados aqui porque o Heroicons não tem alinhamento. --}}
@php
    $alignIcons = [
        'left' => ['Alinhar à esquerda', '<path d="M4 3v18"/><rect x="7" y="6" width="12" height="4" rx="1"/><rect x="7" y="14" width="7" height="4" rx="1"/>'],
        'center' => ['Centralizar na horizontal', '<path d="M12 3v18"/><rect x="5" y="6" width="14" height="4" rx="1"/><rect x="8" y="14" width="8" height="4" rx="1"/>'],
        'right' => ['Alinhar à direita', '<path d="M20 3v18"/><rect x="5" y="6" width="12" height="4" rx="1"/><rect x="10" y="14" width="7" height="4" rx="1"/>'],
        'top' => ['Alinhar ao topo', '<path d="M3 4h18"/><rect x="6" y="7" width="4" height="12" rx="1"/><rect x="14" y="7" width="4" height="7" rx="1"/>'],
        'middle' => ['Centralizar na vertical', '<path d="M3 12h18"/><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="8" width="4" height="8" rx="1"/>'],
        'bottom' => ['Alinhar à base', '<path d="M3 20h18"/><rect x="6" y="5" width="4" height="12" rx="1"/><rect x="14" y="10" width="4" height="7" rx="1"/>'],
    ];
@endphp
@foreach ($alignIcons as $mode => [$label, $paths])
    <button type="button" class="ps-icon-btn" @click="align('{{ $mode }}')" title="{{ $label }}" aria-label="{{ $label }}">
        <svg class="ps-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths !!}</svg>
    </button>
@endforeach
<template x-if="selection.length >= 3">
    <span style="display: contents">
        <button type="button" class="ps-icon-btn" @click="distribute('h')" title="Distribuir na horizontal" aria-label="Distribuir na horizontal">{!! svg('heroicon-o-arrows-right-left', 'ps-i')->toHtml() !!}</button>
        <button type="button" class="ps-icon-btn" @click="distribute('v')" title="Distribuir na vertical" aria-label="Distribuir na vertical">{!! svg('heroicon-o-arrows-up-down', 'ps-i')->toHtml() !!}</button>
    </span>
</template>
