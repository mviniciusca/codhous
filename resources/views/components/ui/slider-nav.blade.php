{{-- Botões anterior/próximo de um Swiper. As classes "{id}-prev" e "{id}-next" ligam ao slider. --}}
@props(['id'])
<div {{ $attributes->class(['flex items-center gap-2']) }}>
    <button type="button" class="slider-btn {{ $id }}-prev" aria-label="Anterior">
        <i data-lucide="chevron-left" class="h-5 w-5"></i>
    </button>
    <button type="button" class="slider-btn {{ $id }}-next" aria-label="Próximo">
        <i data-lucide="chevron-right" class="h-5 w-5"></i>
    </button>
</div>
