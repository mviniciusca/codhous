@php
    $website = \App\Models\Setting::get('website', []);
    $features = data_get($website, 'features', []);
    $enabled = data_get($features, 'scroll_to_top', true);
    
    $whatsappEnabled = data_get($features, 'whatsapp_widget.enabled', false);
    $bottomPosition = $whatsappEnabled ? 'bottom-[6.5rem]' : 'bottom-6';
@endphp

@if($enabled)
<div x-data="{ 
        isVisible: false,
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }"
    @scroll.window="isVisible = (window.pageYOffset > 300)"
    class="fixed {{ $bottomPosition }} right-6 z-40 transition-all duration-300"
    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'"
    aria-label="Voltar ao topo">
    
    <button @click="scrollToTop()"
            class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/90 text-primary-foreground shadow-lg backdrop-blur hover:bg-primary hover:-translate-y-1 hover:shadow-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            title="Voltar ao topo">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </button>
</div>
@endif
