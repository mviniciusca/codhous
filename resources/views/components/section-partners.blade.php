@props([
    'textColor' => 'light',
    'bgColor' => null,
    'layout' => 'slider',
    'subtitle' => null,
    'title' => null,
    'description' => null,
    'header' => [],
    'items' => [],
])

@php
    $brands = collect();

    if (!empty($items)) {
        // Use os itens passados via ContentSection
        foreach ($items as $item) {
            $brands->push((object)[
                'name' => $item['name'] ?? '',
                'logo' => $item['logo'] ?? null,
                'icon' => $item['icon'] ?? 'building-2',
            ]);
        }
    } else {
        // Fallback para o módulo de Brands
        $brands = \App\Models\Brand::where('is_active', true)->orderBy('sort_order')->get();
    }
    
    // Fallback caso não existam marcas no banco nem no ContentSection
    if ($brands->isEmpty()) {
        $brands = collect([
            (object)['name' => 'MRV Engenharia', 'logo' => null, 'icon' => 'building-2'],
            (object)['name' => 'Construtora Tenda', 'logo' => null, 'icon' => 'hard-hat'],
            (object)['name' => 'Cyrela Brazil', 'logo' => null, 'icon' => 'landmark'],
            (object)['name' => 'Gafisa S.A.', 'logo' => null, 'icon' => 'factory'],
            (object)['name' => 'Even Construtora', 'logo' => null, 'icon' => 'warehouse'],
            (object)['name' => 'Direcional Eng.', 'logo' => null, 'icon' => 'hammer'],
            (object)['name' => 'Cury Construtora', 'logo' => null, 'icon' => 'construction'],
            (object)['name' => 'Plano & Plano', 'logo' => null, 'icon' => 'ruler'],
        ]);
    }
@endphp

@if(!\App\Models\ContentSection::isHidden('partners'))
<section class="{{ $bgColor ?? 'bg-white' }} py-16 overflow-hidden {{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}">
    @if($layout === 'grid')
        @php
            $align = $header['alignment'] ?? 'left';
            $isRight = $align === 'right';
        @endphp
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Coluna de Texto -->
                <div class="{{ $isRight ? 'lg:order-last' : '' }}">
                    <x-ui.section-header 
                        :header="$header ?? []"
                        :fallback-title="$title"
                        :fallback-subtitle="$subtitle"
                        :fallback-description="$description"
                        :text-color="$textColor"
                    />
                </div>

                <!-- Coluna de Logos -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 {{ $isRight ? 'lg:order-first' : '' }}">
                    @foreach($brands as $brand)
                        <div class="flex h-24 items-center justify-center rounded-xl bg-white border border-muted/20 p-4 transition-colors shadow-sm">
                            @if(!empty($brand->logo))
                                <img src="{{ Storage::url($brand->logo) }}" 
                                     alt="{{ $brand->name }}" 
                                     class="max-h-full max-w-full object-contain transition-all duration-300">
                            @else
                                <i data-lucide="{{ $brand->icon ?? 'building-2' }}" class="h-8 w-8 text-muted-foreground/40"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <!-- SLIDER ORIGINAL -->
            @if(!empty($title) || !empty($subtitle) || !empty($description) || !empty($header))
                <x-ui.section-header 
                    :header="$header ?? []"
                    :fallback-title="$title"
                    :fallback-subtitle="$subtitle"
                    :fallback-description="$description"
                    :text-color="$textColor"
                />
            @else
                <p class="mb-8 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground/60">
                    Empresas que confiam no nosso concreto
                </p>
            @endif
        </div>

        <!-- Swiper Container - Full Width -->
        <div class="swiper partners-swiper w-full py-8">
            <div class="swiper-wrapper flex items-center">
                @foreach($brands as $brand)
                    <div class="swiper-slide flex items-center justify-center px-4">
                        <div class="flex h-24 w-full items-center justify-center rounded-xl bg-white border border-muted/20 p-4 transition-all duration-300 shadow-sm hover:scale-105">
                            @if(!empty($brand->logo))
                                <img src="{{ Storage::url($brand->logo) }}" 
                                     alt="{{ $brand->name }}" 
                                     class="max-h-full max-w-full object-contain transition-all duration-300">
                            @else
                                <i data-lucide="{{ $brand->icon ?? 'building-2' }}" class="h-8 w-8 text-muted-foreground/40"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <script>
            document.addEventListener('livewire:navigated', initPartnersSwiper);
            document.addEventListener('DOMContentLoaded', initPartnersSwiper);

            function initPartnersSwiper() {
                if (typeof Swiper !== 'undefined' && document.querySelector('.partners-swiper')) {
                    new Swiper('.partners-swiper', {
                        slidesPerView: 3,
                        spaceBetween: 30,
                        loop: true,
                        speed: 5000,
                        autoplay: {
                            delay: 0,
                            disableOnInteraction: false,
                        },
                        breakpoints: {
                            640: { slidesPerView: 5 },
                            1024: { slidesPerView: 7 },
                        },
                    });
                }
            }
        </script>
        
        <style>
            /* Efeito de movimento contínuo linear */
            .partners-swiper .swiper-wrapper {
                transition-timing-function: linear !important;
            }
        </style>
    @endif
</section>
@endif
