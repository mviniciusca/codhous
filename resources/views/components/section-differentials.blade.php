@props([
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'items' => null,
])

@php
    $displayTitle = $title;
    $displaySubtitle = $subtitle;
    $displayDescription = $description;
    $displayItems = $items;

    // Fallback Primário: Focado em CONCRETO USINADO
    if (empty($displayItems)) {
        $displayTitle = $displayTitle ?? 'Nossos Pilares';
        $displaySubtitle = $displaySubtitle ?? 'Por que nos escolher';
        $displayDescription = $displayDescription ?? 'Conheça os pilares que sustentam nossa excelência no fornecimento de concreto e logística para obras de todos os portes.';
        $displayItems = [
            [
                'title' => 'Tecnologia do Concreto',
                'icon' => 'flask-conical',
                'description' => 'Produzimos concreto com rigoroso controle tecnológico e FCK garantido, assegurando a máxima resistência para sua estrutura.'
            ],
            [
                'title' => 'Logística de Precisão',
                'icon' => 'truck',
                'description' => 'Frota moderna e monitoramento em tempo real para garantir que o concreto chegue no canteiro no horário exato da concretagem.'
            ],
            [
                'title' => 'Suporte Especialista',
                'icon' => 'users',
                'description' => 'Nossa equipe técnica acompanha sua obra desde o cálculo do volume até o bombeamento, oferecendo a melhor solução técnica.'
            ],
        ];
    }

    // Se mesmo assim estiver vazio (caso alguém tenha limpado o fallback), tenta buscar da seção global
    if (empty($displayItems)) {
        $section = \App\Models\ContentSection::getBySlug('differentials');
        $displayTitle = $displayTitle ?? ($section?->content['header']['title'] ?? 'Nossos Pilares');
        $displaySubtitle = $displaySubtitle ?? ($section?->content['header']['subtitle'] ?? 'Diferenciais');
        $displayDescription = $displayDescription ?? ($section?->content['header']['description'] ?? null);
        $displayItems = $section?->content['items'] ?? null;
    }

    // Garante que só mostramos 3 itens
    $displayItems = array_slice((array) $displayItems, 0, 3);
@endphp

<section class="bg-background py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        @if(!empty($displayTitle) || !empty($displaySubtitle) || !empty($displayDescription))
            <div class="mb-16 text-center">
                @if(!empty($displaySubtitle))
                    <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $displaySubtitle }}</span>
                @endif
                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl">
                    {{ $displayTitle }}
                </h2>
                @if(!empty($displayDescription))
                    <p class="mt-4 text-lg text-muted-foreground max-w-2xl mx-auto leading-relaxed">
                        {{ $displayDescription }}
                    </p>
                @endif
            </div>
        @endif
        
        <div class="grid gap-12 md:grid-cols-3">
            @foreach($displayItems as $item)
                <div class="group flex flex-col items-center text-center transition-all hover:-translate-y-1">
                    {{-- Ícone Centralizado --}}
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-all group-hover:scale-110 group-hover:bg-primary group-hover:text-primary-foreground">
                        <i data-lucide="{{ $item['icon'] ?? 'check-circle' }}" class="h-8 w-8"></i>
                    </div>

                    {{-- Texto Centralizado --}}
                    <h3 class="mb-3 font-mono text-xl font-bold text-foreground">{{ $item['title'] ?? '' }}</h3>
                    <p class="max-w-xs text-muted-foreground leading-relaxed">
                        {{ $item['description'] ?? '' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
