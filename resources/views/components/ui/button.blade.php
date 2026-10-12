{{--
    Botão/link do site. Variantes: primary, secondary, outline, ghost, whatsapp.
    Tamanhos: sm, md, lg. Com href vira <a>; sem href, <button>.

    <x-ui.button href="#orcamento" icon="calculator" trailing-icon="arrow-right">Orçamento</x-ui.button>
--}}
@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'trailingIcon' => null,
    'type' => 'button',
    'target' => null,
])
@php
    // Nomes por extenso para o Tailwind não descartar as variantes no build
    $variantClass = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
        'whatsapp' => 'btn-whatsapp',
    ][$variant] ?? 'btn-primary';
    $classes = ['btn', $variantClass, 'btn-sm' => $size === 'sm', 'btn-lg' => $size === 'lg'];
    $external = $target === '_blank';
@endphp

@if($href)
    <a href="{{ $href }}" @if($target) target="{{ $target }}" @endif @if($external) rel="noopener noreferrer" @endif {{ $attributes->class($classes) }}>
        @if($icon)<i data-lucide="{{ $icon }}"></i>@endif
        {{ $slot }}
        @if($trailingIcon)<i data-lucide="{{ $trailingIcon }}" data-icon-trailing></i>@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if($icon)<i data-lucide="{{ $icon }}"></i>@endif
        {{ $slot }}
        @if($trailingIcon)<i data-lucide="{{ $trailingIcon }}" data-icon-trailing></i>@endif
    </button>
@endif
