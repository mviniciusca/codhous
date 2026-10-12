@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'layout' => 'slider',
    'subtitle' => null,
    'title' => null,
    'description' => null,
    'header' => [],
    'items' => [],
])

@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);

    $brands = collect($items)->map(fn ($item) => (object) [
        'name' => $item['name'] ?? '',
        'logo' => $item['logo'] ?? null,
        'icon' => $item['icon'] ?? 'building-2',
    ]);

    // Sem itens no bloco: módulo de Marcas e, por fim, demonstração
    if ($brands->isEmpty()) {
        $brands = \App\Models\Brand::where('is_active', true)->orderBy('sort_order')->get();
    }
    if ($brands->isEmpty()) {
        $brands = collect([
            ['MRV Engenharia', 'building-2'], ['Construtora Tenda', 'hard-hat'], ['Cyrela Brazil', 'landmark'], ['Gafisa S.A.', 'factory'],
            ['Even Construtora', 'warehouse'], ['Direcional Eng.', 'hammer'], ['Cury Construtora', 'construction'], ['Plano & Plano', 'ruler'],
        ])->map(fn ($b) => (object) ['name' => $b[0], 'logo' => null, 'icon' => $b[1]]);
    }

    $hasHeader = filled($title) || filled($subtitle) || filled($header['title'] ?? null);
    // Velocidade constante independente da quantidade de logos
    $duration = max(20, $brands->count() * 4);
@endphp

@if(!\App\Models\ContentSection::isHidden('partners'))
<x-ui.section :data="$data" spacing="sm" :container="$layout === 'grid' ? 'default' : 'none'">
    @if($layout === 'grid')
        @php $isRight = ($header['alignment'] ?? 'left') === 'right'; @endphp
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div @class(['lg:order-last' => $isRight])>
                <x-ui.section-header
                    :header="$header ?? []"
                    :fallback-title="$title"
                    :fallback-subtitle="$subtitle"
                    :fallback-description="$description"
                    class="!mb-0"
                />
            </div>
            <div class="grid grid-cols-2 gap-px overflow-hidden rounded-card border border-border/70 bg-border/70 sm:grid-cols-3" data-reveal>
                @foreach($brands as $brand)
                    <div class="group flex h-24 items-center justify-center bg-card p-5" title="{{ $brand->name }}">
                        @include('components.partials.brand-logo', ['brand' => $brand])
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($hasHeader)
                <x-ui.section-header
                    :header="$header ?? []"
                    default-align="center"
                    :fallback-title="$title"
                    :fallback-subtitle="$subtitle"
                    :fallback-description="$description"
                    class="!mb-8"
                />
            @else
                <p class="mb-6 text-center text-[11px] font-bold uppercase tracking-[0.18em] text-muted-foreground">
                    Empresas que confiam no nosso concreto
                </p>
            @endif
        </div>

        {{-- Faixa contínua em CSS: pausa no hover, sem depender de JS --}}
        <div class="marquee marquee-mask relative flex overflow-hidden" style="--marquee-duration: {{ $duration }}s">
            @foreach([0, 1] as $copy)
                <ul class="flex shrink-0 animate-marquee items-center gap-4 pr-4" @if($copy) aria-hidden="true" @endif>
                    @foreach($brands as $brand)
                        <li class="group flex h-20 w-40 shrink-0 items-center justify-center rounded-card border border-border/60 bg-card px-5 transition-colors hover:border-primary-500/30 md:w-48" title="{{ $brand->name }}">
                            @include('components.partials.brand-logo', ['brand' => $brand])
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    @endif
</x-ui.section>
@endif
