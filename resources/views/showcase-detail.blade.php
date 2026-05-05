<x-layouts.app>
    {{-- Header da Obra Padronizado --}}
    <x-page-header 
        :title="$showcase->title" 
        :description="$showcase->description" 
        :badge="$showcase->location" 
        :breadcrumbs="[
            ['label' => 'Nossas Obras', 'url' => '/nossas-obras'],
            ['label' => $showcase->title]
        ]" 
    />

    {{-- Galeria de Fotos e Vídeos --}}
    <section class="bg-muted/30 py-12 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Vídeos Primeiro --}}
                @if($showcase->videos && count($showcase->videos) > 0)
                    @foreach($showcase->videos as $video)
                        <a href="{{ $video['url'] }}" 
                           class="glightbox group relative aspect-video overflow-hidden rounded-2xl bg-zinc-900 shadow-sm transition-all hover:shadow-xl sm:col-span-2 lg:col-span-1"
                           data-gallery="showcase-gallery">
                            {{-- Placeholder/Thumbnail de vídeo (Pode ser melhorado se tivermos thumbnails) --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-900/40 text-center p-6">
                                <div class="mb-4 rounded-full bg-primary/20 p-6 backdrop-blur-md transition-transform group-hover:scale-110">
                                    <i data-lucide="play" class="h-10 w-10 text-primary fill-primary"></i>
                                </div>
                                <span class="font-mono text-sm font-bold uppercase tracking-widest text-white">{{ $video['title'] ?? 'Ver Vídeo' }}</span>
                            </div>
                            
                            {{-- Overlay de Interação --}}
                            <div class="absolute inset-0 bg-zinc-950/20 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        </a>
                    @endforeach
                @endif

                {{-- Fotos --}}
                @if($showcase->images && count($showcase->images) > 0)
                    @foreach($showcase->images as $index => $image)
                        @php 
                            $imageUrl = str_starts_with($image, 'http') ? $image : Storage::url($image);
                        @endphp
                        <a href="{{ $imageUrl }}" 
                           class="glightbox group relative aspect-[4/5] overflow-hidden rounded-2xl bg-card shadow-sm transition-all hover:shadow-xl {{ ($index === 0 && empty($showcase->videos)) ? 'sm:col-span-2 sm:aspect-video' : '' }}"
                           data-gallery="showcase-gallery">
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $showcase->title }} - Foto {{ $index + 1 }}" 
                                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                            
                            {{-- Lupa de zoom --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-zinc-950/20 opacity-0 transition-opacity group-hover:opacity-100">
                                <div class="rounded-full bg-background/80 p-4 backdrop-blur-md">
                                    <i data-lucide="maximize-2" class="h-6 w-6 text-foreground"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            @if((!$showcase->images || count($showcase->images) === 0) && (!$showcase->videos || count($showcase->videos) === 0))
                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border py-20 text-center">
                    <i data-lucide="image-off" class="h-12 w-12 text-muted-foreground/30"></i>
                    <p class="mt-4 text-muted-foreground">Nenhum conteúdo disponível para esta obra.</p>
                </div>
            @endif
        </div>
    </section>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true
            });
        });

        // Inicialização imediata caso não use navigation
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: true
        });
    </script>

    {{-- CTA Final --}}
    <section class="bg-background py-16 border-t border-border">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 text-center">
            <h2 class="font-mono text-3xl font-bold text-foreground">Gostou deste projeto?</h2>
            <p class="mt-4 text-muted-foreground max-w-xl mx-auto">
                Podemos entregar a mesma qualidade e agilidade na sua obra. Solicite um orçamento sem compromisso.
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="/#orcamento" class="rounded-lg bg-primary px-8 py-4 text-sm font-bold uppercase tracking-widest text-primary-foreground shadow-lg transition-all hover:scale-105">
                    Solicitar Orçamento
                </a>
                <a href="/contato" class="rounded-lg border border-border bg-background px-8 py-4 text-sm font-bold uppercase tracking-widest text-foreground transition-all hover:bg-muted">
                    Falar com Especialista
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
