@php
    $layout = $layout ?? 'default';
    $isWhatsapp = $layout === 'whatsapp';
@endphp
<section class="relative flex min-h-[90vh] items-center overflow-hidden bg-foreground pt-16">
    <div class="pointer-events-none absolute inset-0 opacity-5">
        <div class="absolute left-1/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-0 top-1/3 h-px w-full bg-background"></div>
        <div class="absolute left-0 top-2/3 h-px w-full bg-background"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-20 lg:px-8">
        @if($isWhatsapp)
            {{-- Layout WhatsApp: conteúdo centralizado + CEP abaixo --}}
            <div class="flex flex-col items-center gap-10 text-center">
                <div class="max-w-3xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        <span class="text-xs font-medium uppercase tracking-wider text-primary">{{ $badge }}</span>
                    </div>
                    <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                        {!! e(data_get($mainSlide, 'title', 'Concreto usinado na sua obra')) !!}
                    </h1>
                    <p class="mt-6 mx-auto max-w-2xl text-lg leading-relaxed text-background/60">
                        {{ data_get($mainSlide, 'subtitle', '') }}
                    </p>
                    @if(!empty($stats))
                        <div class="mt-10 flex flex-wrap items-center justify-center gap-6 lg:gap-10">
                            @foreach($stats as $index => $stat)
                                @if($index > 0)<div class="h-10 w-px bg-background/10"></div>@endif
                                <div class="text-center">
                                    <p class="font-mono text-3xl font-bold {{ $index === 0 ? 'text-primary' : 'text-background' }}">{{ $stat['value'] ?? '' }}</p>
                                    <p class="text-xs text-background/50">{{ $stat['label'] ?? '' }}</p>
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
                    <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                        {!! str_replace('agilidade', '<span class="text-primary">agilidade</span>', e(data_get($mainSlide, 'title', 'Concreto usinado com agilidade e precisão no traço'))) !!}
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-background/60">
                        {{ data_get($mainSlide, 'subtitle', 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado.') }}
                    </p>
                    @if(!empty($stats))
                        <div class="mt-10 flex flex-wrap items-center gap-6 lg:gap-10">
                            @foreach($stats as $index => $stat)
                                @if($index > 0)<div class="h-10 w-px bg-background/10"></div>@endif
                                <div>
                                    <p class="font-mono text-3xl font-bold {{ $index === 0 ? 'text-primary' : 'text-background' }}">{{ $stat['value'] ?? '' }}</p>
                                    <p class="text-xs text-background/50">{{ $stat['label'] ?? '' }}</p>
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
