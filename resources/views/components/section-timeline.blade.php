@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'header' => null,
    'steps' => null,
])
@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $section = \App\Models\ContentSection::getBySlug('timeline');
    $header = $header ?: ($section?->content['header'] ?? []);
    $steps = $steps ?: ($section?->content['steps'] ?? null);

    if (empty($steps)) {
        $header = ['subtitle' => 'Processo Simplificado', 'title' => 'Como funciona', 'description' => 'Do orçamento à entrega, tudo pensado para facilitar sua obra.'];
        $steps = [
            ['step_label' => 'Etapa 1', 'title' => 'Solicite o Orçamento', 'description' => 'Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra.', 'icon' => 'message-square-text'],
            ['step_label' => 'Etapa 2', 'title' => 'Aprovação do Traço', 'description' => 'Nossos engenheiros definem o traço ideal para a necessidade e o tipo da sua estrutura.', 'icon' => 'clipboard-check'],
            ['step_label' => 'Etapa 3', 'title' => 'Produção na Usina', 'description' => 'O concreto é produzido com controle de qualidade rigoroso e rastreabilidade total.', 'icon' => 'factory'],
            ['step_label' => 'Etapa 4', 'title' => 'Entrega na Obra', 'description' => 'Frota própria com rastreamento entrega o concreto no horário combinado.', 'icon' => 'truck'],
        ];
    }

    $steps = array_values((array) $steps);
    $count = max(count($steps), 1);
    $cols = match (min($count, 5)) {
        1 => 'lg:grid-cols-1',
        2 => 'lg:grid-cols-2',
        3 => 'lg:grid-cols-3',
        4 => 'lg:grid-cols-4',
        default => 'lg:grid-cols-5',
    };
@endphp
@if(!\App\Models\ContentSection::isHidden('timeline'))
<x-ui.section :data="$data" id="como-funciona" background="bg-background">
    <x-ui.section-header
        :header="$header"
        default-align="center"
        :fallback-title="$header['title'] ?? 'Como funciona'"
        :fallback-subtitle="$header['subtitle'] ?? null"
        :fallback-description="$header['description'] ?? null"
    />

    <ol class="timeline relative grid gap-8 lg:gap-6 {{ $cols }}" data-reveal>
        {{-- Trilho horizontal (desktop): o preenchimento corre ao entrar na tela --}}
        <span class="absolute top-7 hidden h-px bg-border lg:block" style="left: calc(100% / {{ $count }} / 2); right: calc(100% / {{ $count }} / 2);" aria-hidden="true">
            <span class="timeline-progress absolute inset-0 origin-left bg-gradient-to-r from-primary-400 via-primary-500 to-primary-700"></span>
        </span>
        {{-- Trilho vertical (mobile) --}}
        <span class="absolute bottom-7 left-7 top-7 w-px bg-border lg:hidden" aria-hidden="true"></span>

        @foreach($steps as $index => $step)
            <li class="group relative flex gap-5 lg:flex-col lg:items-center lg:gap-0 lg:text-center">
                <span class="timeline-node relative z-10 flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-border bg-card text-primary-ink shadow-sm transition-all duration-300 group-hover:-translate-y-1 group-hover:border-primary-500 group-hover:bg-primary-500 group-hover:text-primary-foreground group-hover:shadow-lg group-hover:shadow-primary-600/25 lg:mb-5">
                    <i data-lucide="{{ $step['icon'] ?? 'circle' }}" class="h-6 w-6"></i>
                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 font-mono text-[10px] font-bold text-white ring-2 ring-background">{{ $index + 1 }}</span>
                </span>
                <div class="pt-1 lg:pt-0">
                    <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-primary-ink">{{ $step['step_label'] ?? 'Etapa ' . ($index + 1) }}</span>
                    <h3 class="mt-1 text-lg font-bold text-foreground">{{ $step['title'] ?? '' }}</h3>
                    <p class="mx-auto mt-1.5 max-w-xs text-sm leading-relaxed text-muted-foreground">{{ $step['description'] ?? '' }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</x-ui.section>
@endif
