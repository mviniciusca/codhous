@php
    $items = $showcases->items();
@endphp

<div>
    @if(count($items) > 0)
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $showcase)
                <article class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-all hover:border-primary/40 hover:shadow-xl">
                    {{-- Galeria de Imagens --}}
                    @if($showcase->images && count($showcase->images) > 0)
                        <div class="relative aspect-video overflow-hidden">
                            @php 
                                $firstImage = $showcase->images[0];
                                $imageUrl = str_starts_with($firstImage, 'http') ? $firstImage : Storage::url($firstImage);
                            @endphp
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $showcase->title }}" 
                                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                            
                            {{-- Overlay info --}}
                            @if(count($showcase->images) > 1)
                                <div class="absolute bottom-3 right-3 rounded-full bg-background/80 px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest text-foreground backdrop-blur-sm">
                                    +{{ count($showcase->images) - 1 }} fotos
                                </div>
                            @endif

                            @if($showcase->location)
                                <div class="absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full bg-primary px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-primary-foreground shadow-lg">
                                    <i data-lucide="map-pin" class="h-2.5 w-2.5"></i>
                                    {{ $showcase->location }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="font-mono text-lg font-bold tracking-tight text-foreground transition-colors group-hover:text-primary">
                            {{ $showcase->title }}
                        </h3>
                        
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted-foreground line-clamp-2">
                            {{ $showcase->description }}
                        </p>

                        <div class="mt-6 pt-4 border-t border-border flex items-center justify-between">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-muted-foreground">Concluído</span>
                            <a href="{{ route('showcase.show', $showcase->id) }}" class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-primary transition-all hover:gap-3">
                                Ver Detalhes <i data-lucide="arrow-right" class="h-3 w-3"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Paginação Condicional --}}
        @if(($showPagination ?? true) && $showcases->hasPages())
            <div class="mt-16 flex justify-center">
                {{ $showcases->links() }}
            </div>
        @endif
    @else
        {{-- Empty State (Homepage Pattern) --}}
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-muted/30 py-20 text-center">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-background shadow-sm">
                <i data-lucide="camera" class="h-10 w-10 text-muted-foreground/40"></i>
            </div>
            <h3 class="font-mono text-2xl font-bold text-foreground">Nenhuma obra encontrada</h3>
            <p class="mt-2 max-w-sm text-muted-foreground">
                Estamos preparando o portfólio de novos projetos. Em breve você verá nossas obras de excelência aqui.
            </p>
        </div>
    @endif
</div>
