@php
    $brands = \App\Models\Brand::where('is_active', true)->orderBy('sort_order')->get();
    
    // Fallback caso não existam marcas no banco
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

<section class="border-y border-border bg-muted/30 py-12 overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <p class="mb-10 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground/60">
            Empresas que confiam no nosso concreto
        </p>
    </div>

    <!-- Swiper Container - Full Width -->
    <div class="swiper partners-swiper w-full">
            <div class="swiper-wrapper flex items-center">
                @foreach($brands as $brand)
                    <div class="swiper-slide flex items-center justify-center px-6">
                        <div class="flex items-center transition-all duration-300 hover:scale-110">
                            @if(!empty($brand->logo))
                                <div class="flex h-14 w-full items-center justify-center">
                                    <img src="{{ Storage::url($brand->logo) }}" 
                                         alt="{{ $brand->name }}" 
                                         class="h-full max-w-[160px] object-contain">
                                </div>
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded bg-foreground/5">
                                    <i data-lucide="{{ $brand->icon ?? 'building-2' }}" class="h-7 w-7 text-foreground/40"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', initPartnersSwiper);
        document.addEventListener('DOMContentLoaded', initPartnersSwiper);

        function initPartnersSwiper() {
            if (typeof Swiper !== 'undefined') {
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
</section>
