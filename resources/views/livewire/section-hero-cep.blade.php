@php
    $layout = $layout ?? 'default';
    $alignment = $alignment ?? 'center';
    $isWhatsapp = $layout === 'whatsapp';
    $alignments = [
        'center' => 'object-center',
        'top' => 'object-top',
        'bottom' => 'object-bottom',
        'left' => 'object-left',
        'right' => 'object-right',
    ];
    $alignmentClass = $alignments[$mainSlide['image_alignment'] ?? 'center'] ?? 'object-center';
    
    $textAlignClass = $alignment === 'left' ? 'text-left lg:text-left' : ($alignment === 'right' ? 'text-right lg:text-right' : 'text-center lg:text-center');
    $flexAlignClass = $alignment === 'left' ? 'items-start' : ($alignment === 'right' ? 'items-end' : 'items-center');
    $marginClass = $alignment === 'left' ? 'mr-auto ml-0' : ($alignment === 'right' ? 'ml-auto mr-0' : 'mx-auto');
    $justifyClass = $alignment === 'left' ? 'justify-start' : ($alignment === 'right' ? 'justify-end' : 'justify-center');
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('.hero-swiper')) {
                new Swiper('.hero-swiper', {
                    loop: true,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                });
            }
        });
    </script>
@endpush
    @php
        // Sobreposição clara pede texto escuro; nos demais casos o texto é claro sobre a mídia
        $heroLight = ($overlayEnabled ?? true) && ($overlayTheme ?? 'dark') === 'light';
        $isClean = $layout === 'clean';
        $mediaUrl = fn ($path) => str_starts_with($path, 'http') ? $path : Storage::url($path);
    @endphp
    <section class="relative isolate flex min-h-[68vh] items-center overflow-hidden {{ $bgColor ?: 'bg-primary-950' }} {{ $heroLight ? 'text-scheme-light' : 'dark' }} {{ $spacing ?: 'py-14 md:py-20' }}">

        {{-- Mídia de fundo --}}
        <div class="absolute inset-0 -z-10">
            @if($showSlideshow && !empty($slideshow))
                <div class="swiper hero-swiper h-full w-full">
                    <div class="swiper-wrapper">
                        @foreach($slideshow as $slide)
                            <div class="swiper-slide h-full w-full bg-primary-950">
                                @if(!empty($slide['image']))
                                    <img src="{{ $mediaUrl($slide['image']) }}" class="h-full w-full object-cover {{ $alignments[$slide['image_alignment'] ?? 'center'] ?? 'object-center' }}" alt="">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(!empty($mainSlide['video']))
                <video autoplay muted loop playsinline class="h-full w-full object-cover {{ $alignmentClass }}">
                    <source src="{{ $mediaUrl($mainSlide['video']) }}" type="video/mp4">
                </video>
            @elseif(!empty($mainSlide['image']))
                <img src="{{ $mediaUrl($mainSlide['image']) }}" class="h-full w-full object-cover {{ $alignmentClass }}" alt="{{ $mainSlide['title'] ?? '' }}">
            @else
                {{-- Sem mídia: gradiente com os tons profundos da marca --}}
                <div class="h-full w-full bg-gradient-to-br from-primary-900 via-primary-950 to-black"></div>
                <div class="absolute -right-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-primary-500/30 blur-3xl"></div>
                <div class="absolute inset-0 opacity-[0.06] [background-image:radial-gradient(white_1px,transparent_1px)] [background-size:22px_22px]"></div>
            @endif

            @if($overlayEnabled ?? true)
                @if($heroLight)
                    <div class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-r from-white/90 via-white/70 to-white/20"></div>
                @else
                    <div class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-r from-black/80 via-black/55 to-black/10"></div>
                    <div class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-t from-primary-950/60 via-transparent to-transparent"></div>
                @endif
            @endif
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div @class([
                'flex flex-col gap-10',
                'items-center' => $isClean,
                'lg:flex-row lg:items-center lg:justify-between lg:gap-16' => ! $isClean,
            ])>
                <div @class([
                    'w-full flex-1',
                    $textAlignClass,
                    "flex max-w-4xl flex-col {$flexAlignClass} {$marginClass}" => $isClean,
                    'max-w-2xl' => ! $isClean,
                ]) data-reveal>
                    @if(!empty($badge))
                        <span class="kicker kicker-pill mb-6 backdrop-blur"><span class="kicker-dot"></span> {{ $badge }}</span>
                    @endif

                    <h1 class="font-mono text-4xl font-bold leading-[1.05] tracking-tight text-foreground sm:text-5xl lg:text-6xl" style="text-wrap: balance;">
                        {{ data_get($mainSlide, 'title') ?: 'Concreto usinado na sua obra' }}
                    </h1>

                    @if(filled(data_get($mainSlide, 'subtitle')))
                        <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted-foreground {{ $marginClass }}">
                            {{ data_get($mainSlide, 'subtitle') }}
                        </p>
                    @endif

                    @if($showActionButtons && (!empty($buttons['primary']['text']) || !empty($buttons['secondary']['text'])))
                        <div class="mt-8 flex flex-wrap items-center gap-3 {{ $justifyClass }}">
                            @if(!empty($buttons['primary']['text']))
                                <x-ui.button :href="$buttons['primary']['url'] ?? '#'" size="lg" :icon="$buttons['primary']['icon'] ?: null" :trailing-icon="empty($buttons['primary']['icon']) ? 'arrow-right' : null" class="w-full sm:w-auto">
                                    {{ $buttons['primary']['text'] }}
                                </x-ui.button>
                            @endif
                            @if(!empty($buttons['secondary']['text']))
                                <x-ui.button :href="$buttons['secondary']['url'] ?? '#'" variant="secondary" size="lg" :icon="$buttons['secondary']['icon'] ?: null" class="w-full backdrop-blur sm:w-auto">
                                    {{ $buttons['secondary']['text'] }}
                                </x-ui.button>
                            @endif
                        </div>
                    @endif

                    @if($showStats && !empty($stats))
                        <dl class="mt-10 flex items-center gap-x-4 sm:flex-wrap sm:gap-x-8 sm:gap-y-4 {{ $justifyClass }}">
                            @foreach($stats as $index => $stat)
                                <div @class(['flex min-w-0 flex-col', 'border-l border-border pl-4 sm:pl-8' => $index > 0])>
                                    <dt class="order-2 text-xs text-muted-foreground">{{ $stat['label'] ?? '' }}</dt>
                                    <dd class="order-1 font-mono text-2xl font-bold text-primary-ink md:text-3xl" data-count>{{ $stat['value'] ?? '' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </div>

                @if(! $isClean)
                    <div class="mx-auto w-full max-w-md shrink-0 lg:mx-0" data-reveal style="--reveal-delay: 150ms">
                        @if($isWhatsapp)
                            @include('livewire.partials.hero-whatsapp-card', ['theme' => $heroLight ? 'corporate' : 'default'])
                        @else
                            @include('livewire.partials.hero-cep-card', ['theme' => $heroLight ? 'corporate' : 'default'])
                        @endif
                    </div>
                @endif
            </div>
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
