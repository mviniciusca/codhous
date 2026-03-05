@php
    $section = \App\Models\ContentSection::getBySlug('coverage');
    $header = $section?->content['header'] ?? [];
    $cities = $section?->content['cities'] ?? null;
    $sidebar = $section?->content['sidebar'] ?? null;
    if (empty($cities)) {
        $header = ['subtitle' => 'Cobertura', 'title' => 'Onde atendemos', 'description' => 'Atuamos na região com frota própria e logística integrada para garantir entrega no prazo.'];
        $cities = [
            ['label' => 'São Paulo (capital e região metropolitana)'],
            ['label' => 'Guarulhos e ABC Paulista'],
            ['label' => 'Osasco, Barueri e região Oeste'],
            ['label' => 'Campinas e região'],
            ['label' => 'Santos e Baixada Santista'],
            ['label' => 'Outras regiões sob consulta'],
        ];
    }
    if (empty($sidebar)) {
        $sidebar = [
            ['title' => 'Raio de entrega', 'description' => 'Consulte disponibilidade e prazo para sua cidade no orçamento.'],
            ['title' => 'Frota própria', 'description' => 'Rastreamento e pontualidade em todas as entregas.'],
        ];
    }
@endphp
<section id="onde-atuamos" class="border-b border-border bg-muted/50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            @if(!empty($header['subtitle']))
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $header['subtitle'] }}</span>
            @endif
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">{{ $header['title'] ?? 'Onde atendemos' }}</h2>
            @if(!empty($header['description']))
                <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">{{ $header['description'] }}</p>
            @endif
        </div>

        <div class="flex flex-col gap-12 lg:flex-row lg:items-start lg:gap-16">
            <div class="flex-1">
                <div class="rounded-xl border border-border bg-card p-6 lg:p-8">
                    <h3 class="mb-6 font-mono text-lg font-bold text-foreground">Principais cidades e regiões</h3>
                    <ul class="grid gap-2 text-sm text-muted-foreground sm:grid-cols-2">
                        @foreach($cities as $city)
                            <li class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                                {{ $city['label'] ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="flex flex-col gap-6 lg:w-80">
                @foreach($sidebar as $index => $card)
                    @php
                        $icons = ['map-pin', 'truck'];
                        $icon = $card['icon'] ?? ($icons[$index] ?? 'map-pin');
                    @endphp
                    <div class="flex items-start gap-4 rounded-lg border border-border bg-card p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                            <i data-lucide="{{ $icon }}" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div>
                            <p class="font-mono text-sm font-bold text-foreground">{{ $card['title'] ?? '' }}</p>
                            @if(!empty($card['description']))
                                <p class="mt-1 text-sm text-muted-foreground">{{ $card['description'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
