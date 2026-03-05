@php
    $section = \App\Models\ContentSection::getBySlug('services');
    $header = $section?->content['header'] ?? [];
    $items = $section?->content['items'] ?? null;
    if (empty($items)) {
        $header = [
            'subtitle' => 'Nossos Serviços',
            'title' => 'Tudo que sua obra precisa em um só lugar',
            'description' => 'Da fundação ao acabamento, oferecemos soluções completas com qualidade garantida.',
        ];
        $items = [
            ['title' => 'Concreto Usinado', 'subtitle' => 'por m³', 'description' => 'Concreto de alta qualidade com controle rigoroso de traço e resistência. Todos os tipos de FCK disponíveis para sua obra.', 'icon' => 'droplets', 'bullets' => ['FCK 20 a 50 MPa', 'Traço personalizado', 'Nota fiscal e certificado'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
            ['title' => 'Bombeamento de Concreto', 'subtitle' => 'por serviço', 'description' => 'Serviço de bombeamento para lajes, fundações e estruturas em altura. Equipamentos modernos e operadores certificados.', 'icon' => 'gauge', 'bullets' => ['Bomba estacionária', 'Bomba lança', 'Até 40m de altura'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
            ['title' => 'Locação de Máquinas', 'subtitle' => 'diária / hora', 'description' => 'Equipamentos de ponta para sua obra. Retroescavadeiras, minicarregadeiras, compactadores e muito mais.', 'icon' => 'wrench', 'bullets' => ['Retroescavadeira', 'Minicarregadeira', 'Compactador de solo'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
        ];
    }
@endphp
<section id="servicos" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 max-w-2xl">
            @if(!empty($header['subtitle']))
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $header['subtitle'] }}</span>
            @endif
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">{{ $header['title'] ?? 'Nossos Serviços' }}</h2>
            @if(!empty($header['description']))
                <p class="mt-4 text-lg leading-relaxed text-muted-foreground">{{ $header['description'] }}</p>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $service)
                <div class="group flex flex-col rounded-lg border border-border bg-card p-8 transition-all hover:border-primary/40 hover:shadow-lg">
                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-lg bg-primary/10">
                        <i data-lucide="{{ $service['icon'] ?? 'droplets' }}" class="h-7 w-7 text-primary"></i>
                    </div>
                    <h3 class="font-mono text-xl font-bold text-card-foreground">{{ $service['title'] ?? '' }}</h3>
                    @if(!empty($service['subtitle']))
                        <span class="mt-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ $service['subtitle'] }}</span>
                    @endif
                    <p class="mt-4 flex-1 text-sm leading-relaxed text-muted-foreground">{{ $service['description'] ?? '' }}</p>
                    @if(!empty($service['bullets']))
                        <ul class="mt-6 flex flex-col gap-2 border-t border-border pt-6">
                            @foreach((array) $service['bullets'] as $bullet)
                                <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="{{ $service['cta_url'] ?? '#orcamento' }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                        {{ $service['cta_label'] ?? 'Solicitar Orçamento' }} <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
