@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'header' => [],
    'items' => null,
])

@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $displayTitle = $title;
    $displaySubtitle = $subtitle;
    $displayDescription = $description;
    $displayItems = $items;

    // Sem itens no bloco: tenta a seção global e, por fim, um conteúdo de demonstração
    if (empty($displayItems)) {
        $section = \App\Models\ContentSection::getBySlug('differentials');
        $displayItems = $section?->content['items'] ?? null;
        $displayTitle ??= $section?->content['header']['title'] ?? null;
        $displaySubtitle ??= $section?->content['header']['subtitle'] ?? null;
        $displayDescription ??= $section?->content['header']['description'] ?? null;
    }

    if (empty($displayItems)) {
        $displayTitle ??= 'Nossos Pilares';
        $displaySubtitle ??= 'Por que nos escolher';
        $displayDescription ??= 'Conheça os pilares que sustentam nossa excelência no fornecimento de concreto e logística para obras de todos os portes.';
        $displayItems = [
            ['title' => 'Tecnologia do Concreto', 'icon' => 'flask-conical', 'description' => 'Produzimos concreto com rigoroso controle tecnológico e FCK garantido, assegurando a máxima resistência para sua estrutura.'],
            ['title' => 'Logística de Precisão', 'icon' => 'truck', 'description' => 'Frota moderna e monitoramento em tempo real para garantir que o concreto chegue no canteiro no horário exato da concretagem.'],
            ['title' => 'Suporte Especialista', 'icon' => 'users', 'description' => 'Nossa equipe técnica acompanha sua obra desde o cálculo do volume até o bombeamento, oferecendo a melhor solução técnica.'],
        ];
    }

    $displayItems = array_slice((array) $displayItems, 0, 3);
@endphp

@if(!\App\Models\ContentSection::isHidden('differentials'))
<x-ui.section :data="$data" background="bg-background">
    <x-ui.section-header
        :header="$header ?? []"
        :fallback-title="$displayTitle"
        :fallback-subtitle="$displaySubtitle"
        :fallback-description="$displayDescription"
    />

    {{-- Pilares lado a lado num único painel, separados por linhas finas --}}
    <div class="surface-card grid gap-px overflow-hidden bg-border/70 md:grid-cols-3" data-reveal>
        @foreach($displayItems as $index => $item)
            <div class="group relative bg-card p-6 transition-colors duration-300 hover:bg-primary-500/[0.03] md:p-8" data-spotlight>
                <div class="mb-6 flex items-center justify-between">
                    <x-ui.icon-box size="lg" :icon="$item['icon'] ?? 'check-circle'" />
                    <span class="font-mono text-sm font-medium text-muted-foreground/40 transition-colors group-hover:text-primary-ink">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h3 class="text-lg font-bold text-foreground">{{ $item['title'] ?? '' }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ $item['description'] ?? '' }}</p>
                {{-- Linha de destaque que cresce no hover --}}
                <span class="absolute inset-x-0 bottom-0 h-0.5 origin-left scale-x-0 bg-gradient-to-r from-primary-400 to-primary-600 transition-transform duration-500 ease-out-expo group-hover:scale-x-100"></span>
            </div>
        @endforeach
    </div>
</x-ui.section>
@endif
