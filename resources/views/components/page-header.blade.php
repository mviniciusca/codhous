@props([
    'badge'           => null,
    'title'           => null,
    'description'     => null,
    'breadcrumbs'     => [],
    'backgroundImage' => null,
])

@php
    $bgUrl = $backgroundImage ? \Illuminate\Support\Facades\Storage::url($backgroundImage) : null;
@endphp

{{--
    Template de Cabeçalho Padrão das Páginas Internas
--}}
<section class="relative bg-background pt-8 pb-6 lg:pt-10 lg:pb-8 overflow-hidden border-b border-border/40">
    @if($bgUrl)
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="{{ $bgUrl }}" alt="Header Background" class="w-full h-full object-cover opacity-[0.15] mix-blend-multiply dark:mix-blend-lighten">
            <div class="absolute inset-0 bg-gradient-to-r from-background via-background/70 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent opacity-50"></div>
        </div>
    @endif

    <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
        
        {{-- Breadcrumbs --}}
        <nav class="mb-4 flex items-center gap-2 text-[10px] font-medium uppercase tracking-widest text-muted-foreground/50" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition-colors">Início</a>
            @if(!empty($breadcrumbs))
                @foreach($breadcrumbs as $item)
                    <i data-lucide="chevron-right" class="h-3 w-3 opacity-30"></i>
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors">{{ $item['label'] }}</a>
                    @else
                        <span class="text-foreground/70">{{ $item['label'] }}</span>
                    @endif
                @endforeach
            @else
                <i data-lucide="chevron-right" class="h-3 w-3 opacity-30"></i>
                <span class="text-foreground/70">{{ $title }}</span>
            @endif
        </nav>

        <div class="max-w-3xl">
            @if($badge)
                <span class="mb-2 inline-block text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                    {{ $badge }}
                </span>
            @endif

            @if($title)
                <h1 class="font-mono text-2xl font-bold tracking-tight text-foreground md:text-3xl" style="text-wrap: balance;">
                    {{ $title }}
                </h1>
            @endif

            @if($description)
                <p class="mt-2 text-sm leading-relaxed text-muted-foreground/70" style="text-wrap: balance;">
                    {{ $description }}
                </p>
            @endif
        </div>
    </div>
</section>
