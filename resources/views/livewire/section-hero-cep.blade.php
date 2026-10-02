@php
    $layout = $layout ?? 'default';
    $isWhatsapp = $layout === 'whatsapp';
    $alignments = [
        'center' => 'object-center',
        'top' => 'object-top',
        'bottom' => 'object-bottom',
        'left' => 'object-left',
        'right' => 'object-right',
    ];
    $alignmentClass = $alignments[$mainSlide['image_alignment'] ?? 'center'] ?? 'object-center';
@endphp

@php
    $theme = $theme ?? 'default';
@endphp

@if($theme === 'corporate')
    {{-- CORPORATE HERO: Split screen on Desktop, Absolute background on Mobile --}}
    <section class="relative min-h-[80vh] bg-white overflow-hidden flex flex-col lg:flex-row lg:items-center">
        
        {{-- Image (Background on mobile, Right side on desktop) --}}
        <div class="absolute inset-0 z-0 lg:z-10 lg:inset-y-0 lg:left-1/2 lg:right-0 lg:w-1/2 lg:h-auto">
            <div class="h-full w-full absolute inset-0">
                @if(!empty($mainSlide['video']))
                    <video autoplay muted loop playsinline class="h-full w-full object-cover {{ $alignmentClass }}"><source src="{{ str_starts_with($mainSlide['video'], 'http') ? $mainSlide['video'] : Storage::url($mainSlide['video']) }}" type="video/mp4"></video>
                @elseif(!empty($mainSlide['image']))
                    <img src="{{ str_starts_with($mainSlide['image'], 'http') ? $mainSlide['image'] : Storage::url($mainSlide['image']) }}" class="h-full w-full object-cover {{ $alignmentClass }}" alt="{{ $mainSlide['title'] ?? '' }}">
                @else
                    <div class="h-full w-full bg-zinc-200"></div>
                @endif
            </div>
            {{-- Black overlay for mobile --}}
            <div class="absolute inset-0 bg-zinc-950/80 lg:hidden"></div>
        </div>

        {{-- Content --}}
        <div class="mx-auto w-full max-w-7xl px-4 lg:px-8 relative z-20 flex-1 flex flex-col justify-center items-center text-center lg:items-start lg:text-left pt-20 pb-12 lg:py-20 min-h-[80vh] lg:min-h-0">
            <div class="w-full lg:w-1/2 lg:pr-12 xl:pr-16 flex flex-col items-center lg:items-start">
                <div class="mb-6 lg:mb-8 inline-flex items-center gap-2 border-l-4 border-primary pl-4">
                    <span class="text-xs md:text-sm font-bold uppercase tracking-widest text-zinc-300 lg:text-zinc-500">{{ $badge }}</span>
                </div>
                <h1 class="font-sans text-3xl font-extrabold leading-tight tracking-tight text-white lg:text-zinc-900 md:text-4xl lg:text-6xl" style="text-wrap: balance;">
                    {!! str_replace('agilidade', '<span class="text-primary">agilidade</span>', e(data_get($mainSlide, 'title', 'Concreto usinado na sua obra'))) !!}
                </h1>
                <p class="mt-4 lg:mt-6 text-base md:text-lg leading-relaxed text-zinc-300 lg:text-zinc-600">
                    {{ data_get($mainSlide, 'subtitle', 'Entrega rápida, rastreamento em tempo real e suporte especializado.') }}
                </p>
                
                <div class="mt-8 lg:mt-12 w-full max-w-md lg:max-w-none text-left">
                    @if($isWhatsapp)
                        @include('livewire.partials.hero-whatsapp-card')
                    @else
                        @include('livewire.partials.hero-cep-card')
                    @endif
                </div>

                @if(!empty($stats))
                    <div class="mt-10 lg:mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-6 md:gap-8 border-t border-white/20 lg:border-zinc-100 pt-6 lg:pt-8 w-full">
                        @foreach($stats as $index => $stat)
                            <div>
                                <p class="font-mono text-2xl md:text-3xl font-black text-white lg:text-zinc-900">{{ $stat['value'] ?? '' }}</p>
                                <p class="text-[10px] md:text-xs font-semibold uppercase text-zinc-400 lg:text-zinc-500 tracking-wider">{{ $stat['label'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
    </section>

@elseif($theme === 'creative')
    {{-- CREATIVE HERO: Fullscreen, Centered, Huge Typography, Glassmorphism CEP Card --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-zinc-950 pt-20 pb-12">
        <div class="absolute inset-0 z-0">
            <div class="h-full w-full relative">
                @if(!empty($mainSlide['video']))
                    <video autoplay muted loop playsinline class="h-full w-full object-cover {{ $alignmentClass }}"><source src="{{ str_starts_with($mainSlide['video'], 'http') ? $mainSlide['video'] : Storage::url($mainSlide['video']) }}" type="video/mp4"></video>
                @elseif(!empty($mainSlide['image']))
                    <img src="{{ str_starts_with($mainSlide['image'], 'http') ? $mainSlide['image'] : Storage::url($mainSlide['image']) }}" class="h-full w-full object-cover {{ $alignmentClass }}" alt="{{ $mainSlide['title'] ?? '' }}">
                @else
                    <div class="h-full w-full bg-zinc-900"></div>
                @endif
                <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-[2px]"></div>
            </div>
        </div>

        <div class="relative z-20 mx-auto w-full max-w-5xl px-4 flex flex-col items-center text-center">
            <div class="mb-8 inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/10 px-6 py-2 backdrop-blur-md">
                <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-white">{{ $badge }}</span>
            </div>
            
            <h1 class="font-mono text-5xl font-black leading-[1.1] tracking-tighter text-white md:text-7xl lg:text-8xl drop-shadow-2xl" style="text-wrap: balance;">
                {!! e(data_get($mainSlide, 'title', 'Concreto usinado na sua obra')) !!}
            </h1>
            
            <p class="mt-8 mx-auto max-w-2xl text-xl font-medium leading-relaxed text-zinc-300 drop-shadow-md">
                {{ data_get($mainSlide, 'subtitle', '') }}
            </p>

            <div class="mt-16 w-full max-w-lg rounded-3xl bg-white/10 p-6 backdrop-blur-xl border border-white/20 shadow-2xl">
                @if($isWhatsapp)
                    @include('livewire.partials.hero-whatsapp-card')
                @else
                    @include('livewire.partials.hero-cep-card')
                @endif
            </div>
        </div>
    </section>

@else
    {{-- DEFAULT HERO (Original Layout + Layout Whatsapp toggle) --}}
    <section class="relative flex min-h-[70vh] items-center overflow-hidden bg-zinc-950 pt-8">
        
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <div class="h-full w-full relative">
                {{-- Imagem ou Vídeo de fundo --}}
                @if(!empty($mainSlide['video']))
                    <video autoplay muted loop playsinline class="h-full w-full object-cover {{ $alignmentClass }}">
                        <source src="{{ str_starts_with($mainSlide['video'], 'http') ? $mainSlide['video'] : Storage::url($mainSlide['video']) }}" type="video/mp4">
                    </video>
                @elseif(!empty($mainSlide['image']))
                    <img src="{{ str_starts_with($mainSlide['image'], 'http') ? $mainSlide['image'] : Storage::url($mainSlide['image']) }}" 
                         class="h-full w-full object-cover {{ $alignmentClass }}" 
                         alt="{{ $mainSlide['title'] ?? '' }}">
                @else
                    <div class="h-full w-full bg-zinc-900"></div>
                @endif
                
                {{-- Overlay Gradiente --}}
                <div class="absolute inset-0 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/50 to-transparent"></div>
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
                                        <p class="font-mono text-3xl font-bold text-primary">{{ $stat['value'] ?? '' }}</p>
                                        <p class="text-xs text-zinc-400">{{ $stat['label'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    {{-- Card (WhatsApp ou CEP) --}}
                    <div class="w-full max-w-md">
                        @if($isWhatsapp)
                            @include('livewire.partials.hero-whatsapp-card')
                        @else
                            @include('livewire.partials.hero-cep-card')
                        @endif
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
                                        <p class="font-mono text-3xl font-bold text-primary">{{ $stat['value'] ?? '' }}</p>
                                        <p class="text-xs text-zinc-400">{{ $stat['label'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="w-full max-w-md flex-shrink-0">
                        @if($isWhatsapp)
                            @include('livewire.partials.hero-whatsapp-card')
                        @else
                            @include('livewire.partials.hero-cep-card')
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif

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
