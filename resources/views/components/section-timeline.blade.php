@php
    $section = \App\Models\ContentSection::getBySlug('timeline');
    $header = $section?->content['header'] ?? [];
    $steps = $section?->content['steps'] ?? null;
    if (empty($steps)) {
        $header = ['subtitle' => 'Processo Simplificado', 'title' => 'Como funciona', 'description' => 'Do orçamento à entrega, tudo pensado para facilitar sua obra.'];
        $steps = [
            ['step_label' => 'Etapa 1', 'title' => 'Solicite o Orçamento', 'description' => 'Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra.', 'icon' => 'message-square-text'],
            ['step_label' => 'Etapa 2', 'title' => 'Aprovação do Traço', 'description' => 'Nossos engenheiros definem o traço ideal para a necessidade e o tipo da sua estrutura.', 'icon' => 'clipboard-check'],
            ['step_label' => 'Etapa 3', 'title' => 'Produção na Usina', 'description' => 'O concreto é produzido com controle de qualidade rigoroso e rastreabilidade total.', 'icon' => 'factory'],
            ['step_label' => 'Etapa 4', 'title' => 'Entrega na Obra', 'description' => 'Frota própria com rastreamento entrega o concreto no horário combinado.', 'icon' => 'truck'],
        ];
    }
@endphp
<section id="como-funciona" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            @if(!empty($header['subtitle']))
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $header['subtitle'] }}</span>
            @endif
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">{{ $header['title'] ?? 'Como funciona' }}</h2>
            @if(!empty($header['description']))
                <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">{{ $header['description'] }}</p>
            @endif
        </div>

        {{-- Desktop: horizontal timeline --}}
        <div class="hidden lg:block">
            <div class="relative">
                <div class="absolute left-0 right-0 top-10 h-px bg-border"></div>
                <div class="absolute left-0 top-10 h-px bg-primary" style="width: {{ count($steps) > 0 ? min(75, (count($steps) - 1) * 25) : 75 }}%;"></div>
                <div class="grid gap-8" style="grid-template-columns: repeat({{ count($steps) }}, 1fr);">
                    @foreach($steps as $index => $step)
                        <div class="relative flex flex-col items-center text-center">
                            <div class="relative z-10 mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 {{ $index === count($steps) - 1 ? 'border-primary/30' : 'border-primary' }} bg-background">
                                <i data-lucide="{{ $step['icon'] ?? 'circle' }}" class="h-8 w-8 text-primary"></i>
                            </div>
                            <span class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-primary">{{ $step['step_label'] ?? 'Etapa ' . ($index + 1) }}</span>
                            <h3 class="font-mono text-lg font-bold text-foreground">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ $step['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Mobile: vertical timeline --}}
        <div class="lg:hidden">
            <div class="relative ml-10">
                <div class="absolute left-0 top-0 h-full w-px bg-border"></div>
                <div class="flex flex-col gap-10">
                    @foreach($steps as $index => $step)
                        <div class="relative flex gap-6">
                            <div class="absolute -left-6 top-0 z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 {{ $index === count($steps) - 1 ? 'border-primary/30' : 'border-primary' }} bg-background">
                                <i data-lucide="{{ $step['icon'] ?? 'circle' }}" class="h-5 w-5 text-primary"></i>
                            </div>
                            <div class="pl-10">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-primary">{{ $step['step_label'] ?? 'Etapa ' . ($index + 1) }}</span>
                                <h3 class="mt-1 font-mono text-lg font-bold text-foreground">{{ $step['title'] ?? '' }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ $step['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
