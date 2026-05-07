@props([
    'badge'       => null,
    'title'       => null,
    'description' => null,
    'breadcrumbs' => [],
])

{{--
    Template de Cabeçalho Padrão das Páginas Internas
--}}
<section class="bg-background pt-6 pb-6 lg:pt-8 lg:pb-8">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        
        {{-- Breadcrumbs --}}
        <nav class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-muted-foreground" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition-colors">Início</a>
            @if(!empty($breadcrumbs))
                @foreach($breadcrumbs as $item)
                    <i data-lucide="chevron-right" class="h-3 w-3 opacity-50"></i>
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors">{{ $item['label'] }}</a>
                    @else
                        <span class="text-foreground">{{ $item['label'] }}</span>
                    @endif
                @endforeach
            @else
                <i data-lucide="chevron-right" class="h-3 w-3 opacity-50"></i>
                <span class="text-foreground">{{ $title }}</span>
            @endif
        </nav>

        <div class="max-w-3xl">
            @if($badge)
                <span class="mb-2 inline-block text-[10px] font-bold uppercase tracking-widest text-primary">
                    {{ $badge }}
                </span>
            @endif

            @if($title)
                <h1 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                    {{ $title }}
                </h1>
            @endif

            @if($description)
                <p class="mt-3 text-base leading-relaxed text-muted-foreground">
                    {{ $description }}
                </p>
            @endif
        </div>
    </div>
</section>
