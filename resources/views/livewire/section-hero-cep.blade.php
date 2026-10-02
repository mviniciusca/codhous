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



        {{-- Conteúdo (z-20 para ficar na frente de tudo) --}}
        <div class="relative z-20 mx-auto w-full max-w-7xl px-4 py-12 lg:px-8">
                {{-- Layout padrão unificado: texto à esquerda + Card (CEP/WhatsApp) à direita --}}
                <div class="flex flex-col items-center gap-10 lg:flex-row lg:items-center lg:gap-16">
                    <div class="flex-1 text-center lg:text-left">
                        @if(!empty($badge))
                            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                                <span class="h-2 w-2 rounded-full bg-primary"></span>
                                <span class="text-xs font-medium uppercase tracking-wider text-primary">{{ $badge }}</span>
                            </div>
                        @endif
                        <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                            {!! e(data_get($mainSlide, 'title', 'Concreto usinado na sua obra')) !!}
                        </h1>
                        <p class="mt-6 mx-auto lg:mx-0 max-w-xl text-lg leading-relaxed text-zinc-300">
                            {{ data_get($mainSlide, 'subtitle', '') }}
                        </p>
                        @if($showActionButtons && (!empty($buttons['primary']['text']) || !empty($buttons['secondary']['text'])))
                            <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                                @if(!empty($buttons['primary']['text']))
                                    <a href="{{ $buttons['primary']['url'] ?? '#' }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 font-bold text-white shadow-lg transition-all hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-xl">
                                        @if(!empty($buttons['primary']['icon']))
                                            <i data-lucide="{{ $buttons['primary']['icon'] }}" class="h-5 w-5"></i>
                                        @endif
                                        {{ $buttons['primary']['text'] }}
                                    </a>
                                @endif
                                @if(!empty($buttons['secondary']['text']))
                                    <a href="{{ $buttons['secondary']['url'] ?? '#' }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-primary bg-transparent px-6 py-3 font-bold text-primary transition-all hover:-translate-y-0.5 hover:bg-primary/5">
                                        @if(!empty($buttons['secondary']['icon']))
                                            <i data-lucide="{{ $buttons['secondary']['icon'] }}" class="h-5 w-5"></i>
                                        @endif
                                        {{ $buttons['secondary']['text'] }}
                                    </a>
                                @endif
                            </div>
                        @endif
                        @if($showStats && !empty($stats))
                            <div class="mt-10 flex flex-wrap items-center justify-center lg:justify-start gap-6 lg:gap-10">
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
                    <div class="w-full max-w-md flex-shrink-0 mx-auto lg:mx-0">
                        @if($isWhatsapp)
                            @include('livewire.partials.hero-whatsapp-card')
                        @else
                            @include('livewire.partials.hero-cep-card')
                        @endif
                    </div>
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
