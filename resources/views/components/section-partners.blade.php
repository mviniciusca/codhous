@php
    $section = \App\Models\ContentSection::getBySlug('partners');
    $subtitle = $section?->content['header']['subtitle'] ?? 'Empresas que confiam no nosso concreto';
    $items = $section?->content['items'] ?? null;
    if (empty($items)) {
        $items = [
            ['name' => 'MRV Engenharia', 'icon' => 'building-2'],
            ['name' => 'Construtora Tenda', 'icon' => 'hard-hat'],
            ['name' => 'Cyrela Brazil', 'icon' => 'landmark'],
            ['name' => 'Gafisa S.A.', 'icon' => 'factory'],
            ['name' => 'Even Construtora', 'icon' => 'warehouse'],
            ['name' => 'Direcional Eng.', 'icon' => 'hammer'],
            ['name' => 'Cury Construtora', 'icon' => 'construction'],
            ['name' => 'Plano & Plano', 'icon' => 'ruler'],
        ];
    }
@endphp
<section class="border-b border-border bg-muted/50 py-10 lg:py-12 overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <p class="mb-8 text-center text-xs font-semibold uppercase tracking-widest text-muted-foreground">{{ $subtitle }}</p>
    </div>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-50% - 2rem)); }
        }
        .partners-track {
            animation: marquee 30s linear infinite;
            width: max-content;
        }
    </style>

    <script>
        function pauseMarquee() {
            document.getElementById('partners-track').style.animationPlayState = 'paused';
        }
        function resumeMarquee() {
            document.getElementById('partners-track').style.animationPlayState = 'running';
        }
    </script>

    <div class="partners-marquee relative" onmouseenter="pauseMarquee()" onmouseleave="resumeMarquee()">
        <div class="partners-track flex items-center gap-16" id="partners-track">
            @foreach(array_merge($items, $items) as $partner)
                @php $icon = $partner['icon'] ?? 'building-2'; @endphp
                <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="{{ $icon }}" class="h-5 w-5 text-foreground"></i></div>
                    <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">{{ $partner['name'] ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
