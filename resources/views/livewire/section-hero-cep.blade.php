@php
    $layout = $layout ?? 'default';
    $isWhatsapp = $layout === 'whatsapp';
    $slides = $slides ?? [];
    if (empty($slides) && !empty($mainSlide)) {
        $slides = [$mainSlide];
    }
@endphp

<section class="relative flex min-h-[70vh] items-center overflow-hidden bg-zinc-950 pt-8">
    
    {{-- Swiper Background Slider --}}
    <div class="absolute inset-0 z-0">
        <div class="swiper hero-swiper h-full w-full"
             x-data="{ 
                initSwiper() {
                    if (typeof Swiper !== 'undefined') {
                        new Swiper($el, {
                            loop: true,
                            effect: 'fade',
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                            },
                            autoplay: {
                                delay: 5000,
                                disableOnInteraction: false,
                            },
                            speed: 1500,
                        });
                    }
                } 
             }"
             x-init="setTimeout(() => initSwiper(), 200)">
            <div class="swiper-wrapper">
                @forelse($slides as $slide)
                    <div class="swiper-slide relative">
                        {{-- Imagem ou Vídeo de fundo --}}
                        @if(!empty($slide['video']))
                            <video autoplay muted loop playsinline class="h-full w-full object-cover">
                                <source src="{{ str_starts_with($slide['video'], 'http') ? $slide['video'] : Storage::url($slide['video']) }}" type="video/mp4">
                            </video>
                        @elseif(!empty($slide['image']))
                            <img src="{{ str_starts_with($slide['image'], 'http') ? $slide['image'] : Storage::url($slide['image']) }}" 
                                 class="h-full w-full object-cover" 
                                 alt="{{ $slide['title'] ?? '' }}">
                        @else
                            <div class="h-full w-full bg-zinc-900"></div>
                        @endif
                        
                        {{-- Overlay Gradiente --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/50 to-transparent"></div>
                    </div>
                @empty
                    <div class="swiper-slide bg-zinc-900"></div>
                @endforelse
            </div>
            
            {{-- Pontos de Navegação --}}
            <div class="swiper-pagination !bottom-8 !left-auto !right-8 !w-auto"></div>
        </div>
    </div>

    {{-- Grid decorativa por cima do slider --}}
    <div class="pointer-events-none absolute inset-0 z-10 opacity-10">
        <div class="absolute left-1/4 top-0 h-full w-px bg-white"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-white"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-white"></div>
        <div class="absolute left-0 top-1/3 h-px w-full bg-white"></div>
        <div class="absolute left-0 top-2/3 h-px w-full bg-white"></div>
    </div>

    {{-- Conteúdo (z-20 para ficar na frente de tudo) --}}
    <div class="relative z-20 mx-auto w-full max-w-7xl px-4 py-12 lg:px-8">
        @if($isWhatsapp)
            {{-- Layout WhatsApp: conteúdo centralizado + CEP abaixo --}}
            <div class="flex flex-col items-center gap-10 text-center">
                <div class="max-w-3xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        <span class="text-xs font-medium uppercase tracking-wider text-primary">{{ $badge }}</span>
                    </div>
                    <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                        {!! e(data_get($mainSlide, 'title', 'Concreto usinado na sua obra')) !!}
                    </h1>
                    <p class="mt-6 mx-auto max-w-2xl text-lg leading-relaxed text-zinc-300">
                        {{ data_get($mainSlide, 'subtitle', '') }}
                    </p>
                    @if(!empty($stats))
                        <div class="mt-10 flex flex-wrap items-center justify-center gap-6 lg:gap-10">
                            @foreach($stats as $index => $stat)
                                @if($index > 0)<div class="h-10 w-px bg-white/10"></div>@endif
                                <div class="text-center">
                                    <p class="font-mono text-3xl font-bold {{ $index === 0 ? 'text-primary' : 'text-white' }}">{{ $stat['value'] ?? '' }}</p>
                                    <p class="text-xs text-zinc-400">{{ $stat['label'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                {{-- CEP card (sempre visível) --}}
                <div class="w-full max-w-md">
                    @include('livewire.partials.hero-cep-card')
                </div>
            </div>
        @else
            {{-- Layout padrão: texto à esquerda + CEP à direita --}}
            <div class="flex flex-col items-start gap-10 lg:flex-row lg:items-center lg:gap-16">
                <div class="flex-1">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        <span class="text-xs font-medium uppercase tracking-wider text-primary">{{ $badge }}</span>
                    </div>
                    <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                        {!! str_replace('agilidade', '<span class="text-primary">agilidade</span>', e(data_get($mainSlide, 'title', 'Concreto usinado com agilidade e precisão no traço'))) !!}
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-zinc-300">
                        {{ data_get($mainSlide, 'subtitle', 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado.') }}
                    </p>
                    @if(!empty($stats))
                        <div class="mt-10 flex flex-wrap items-center gap-6 lg:gap-10">
                            @foreach($stats as $index => $stat)
                                @if($index > 0)<div class="h-10 w-px bg-white/10"></div>@endif
                                <div>
                                    <p class="font-mono text-3xl font-bold {{ $index === 0 ? 'text-primary' : 'text-white' }}">{{ $stat['value'] ?? '' }}</p>
                                    <p class="text-xs text-zinc-400">{{ $stat['label'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="w-full max-w-md flex-shrink-0">
                    @include('livewire.partials.hero-cep-card')
                </div>
            </div>
        @endif
    </div>
</section>

@script
<script>
    // Reaplica ícones Lucide após atualização do Livewire (ex: "Consultar outro CEP")
    Livewire.hook('morph.updated', () => {
        if (document.getElementById('hero-cep-form') && typeof window.lucide !== 'undefined') {
            window.lucide.createIcons();
        }
    });
</script>
@endscript
