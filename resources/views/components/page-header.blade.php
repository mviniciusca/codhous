@props([
    'badge'           => null,
    'title'           => null,
    'description'     => null,
    'breadcrumbs'     => [],
    'backgroundImage' => null,
])

@php
    $bgUrl = function_exists('resolveFilamentImagePath')
        ? resolveFilamentImagePath($backgroundImage)
        : ($backgroundImage && is_string($backgroundImage) ? \Illuminate\Support\Facades\Storage::url($backgroundImage) : null);
    $trail = !empty($breadcrumbs) ? $breadcrumbs : [['label' => $title]];
@endphp

{{-- Cabeçalho padrão das páginas internas --}}
<section class="relative isolate overflow-hidden border-b border-border/60 bg-background">
    {{-- Fundo: brilho da marca + grade sutil (ou imagem, se houver) --}}
    <div class="pointer-events-none absolute inset-0 -z-10">
        @if($bgUrl)
            <img src="{{ $bgUrl }}" alt="" class="h-full w-full object-cover opacity-[0.12] mix-blend-multiply">
            <div class="absolute inset-0 bg-gradient-to-r from-background via-background/80 to-transparent"></div>
        @else
            <div class="absolute inset-0 opacity-60 [background-image:linear-gradient(to_right,var(--color-border)_1px,transparent_1px),linear-gradient(to_bottom,var(--color-border)_1px,transparent_1px)] [background-size:44px_44px] [mask-image:radial-gradient(ellipse_at_top_right,#000_10%,transparent_65%)]"></div>
        @endif
        <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-primary-500/15 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 md:py-14 lg:px-8">
        <nav class="mb-5 flex flex-wrap items-center gap-1.5 text-xs font-medium text-muted-foreground" aria-label="Breadcrumb">
            <a href="/" class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 transition-colors hover:bg-muted hover:text-primary-ink">
                <i data-lucide="house" class="h-3.5 w-3.5"></i> Início
            </a>
            @foreach($trail as $item)
                <i data-lucide="chevron-right" class="h-3 w-3 opacity-40"></i>
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="rounded-md px-1.5 py-0.5 transition-colors hover:bg-muted hover:text-primary-ink">{{ $item['label'] }}</a>
                @else
                    <span class="px-1.5 py-0.5 text-foreground" aria-current="page">{{ $item['label'] }}</span>
                @endif
            @endforeach
        </nav>

        <div class="max-w-3xl" data-reveal>
            @if($badge)
                <span class="kicker kicker-pill mb-4"><span class="kicker-dot"></span> {{ $badge }}</span>
            @endif

            @if($title)
                <h1 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl lg:text-5xl" style="text-wrap: balance;">{{ $title }}</h1>
            @endif

            @if($description)
                <p class="section-lead mt-3 max-w-2xl">{{ $description }}</p>
            @endif
        </div>
    </div>
</section>
