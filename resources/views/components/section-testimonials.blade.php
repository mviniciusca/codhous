@php
    $section = \App\Models\ContentSection::getBySlug('testimonials');
    $header = $section?->content['header'] ?? [];
    $items = $section?->content['items'] ?? null;
    if (empty($items)) {
        $header = ['subtitle' => 'Depoimentos', 'title' => 'O que dizem nossos clientes', 'description' => 'Empresas e obras que confiam na nossa entrega e no nosso suporte.'];
        $items = [
            ['quote' => '"Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível. Já fechamos várias obras com eles."', 'author_name' => 'Carlos Mendes', 'author_role' => 'Engenheiro — Obra Residencial Alphaville', 'stars' => 5],
            ['quote' => '"Pontualidade e qualidade do traço fazem a diferença. Nosso cronograma não atrasa por causa de concreto."', 'author_name' => 'Ana Paula Costa', 'author_role' => 'Mestre de obras — Construtora regional', 'stars' => 5],
            ['quote' => '"Orçamento claro, entrega no horário e suporte pós-venda. Recomendo para quem não pode perder tempo em obra."', 'author_name' => 'Roberto Lima', 'author_role' => 'Arquiteto — Escritório próprio', 'stars' => 5],
        ];
    }
@endphp
<section id="depoimentos" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            @if(!empty($header['subtitle']))
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $header['subtitle'] }}</span>
            @endif
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">{{ $header['title'] ?? 'Depoimentos' }}</h2>
            @if(!empty($header['description']))
                <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">{{ $header['description'] }}</p>
            @endif
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $index => $item)
                <div class="flex flex-col rounded-xl border border-border bg-card p-8 shadow-sm transition-all hover:border-primary/30 hover:shadow-md {{ $index === 2 ? 'md:col-span-2 lg:col-span-1' : '' }}">
                    <div class="mb-6 flex gap-1 text-primary">
                        @foreach(range(1, (int) ($item['stars'] ?? 5)) as $i)
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                        @endforeach
                    </div>
                    <p class="flex-1 text-sm leading-relaxed text-muted-foreground">{{ $item['quote'] ?? '' }}</p>
                    <div class="mt-6 flex items-center gap-4 border-t border-border pt-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <i data-lucide="user" class="h-6 w-6 text-primary"></i>
                        </div>
                        <div>
                            <p class="font-mono text-sm font-bold text-foreground">{{ $item['author_name'] ?? '' }}</p>
                            @if(!empty($item['author_role']))
                                <p class="text-xs text-muted-foreground">{{ $item['author_role'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
