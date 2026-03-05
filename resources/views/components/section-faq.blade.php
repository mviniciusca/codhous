@php
    $section = \App\Models\ContentSection::getBySlug('faq');
    $header = $section?->content['header'] ?? [];
    $items = $section?->content['items'] ?? null;
    $useStatic = empty($items);
    if ($useStatic) {
        $header = [
            'subtitle' => 'Dúvidas Frequentes',
            'title' => 'Perguntas frequentes',
            'description' => 'Respostas rápidas sobre concreto usinado, entrega e orçamento.',
        ];
        $items = [
            ['question' => 'Qual o prazo para receber o orçamento?', 'answer' => 'Em horário comercial, respondemos em até 2 horas. Para pedidos com volume e endereço definidos, o orçamento pode ser enviado em minutos pelo WhatsApp.'],
            ['question' => 'Vocês entregam no dia que eu precisar?', 'answer' => 'Sim. Trabalhamos com agendamento e nossa logística garante alta taxa de pontualidade. O ideal é solicitar com antecedência para garantir o horário desejado.'],
            ['question' => 'Qual o volume mínimo de concreto?', 'answer' => 'O volume mínimo varia conforme a região e o tipo de serviço. Entre em contato ou use a calculadora no site para simular o volume; nosso time informa o mínimo no orçamento.'],
            ['question' => 'O concreto vem com nota fiscal e certificado?', 'answer' => 'Sim. Todos os carregamentos são acompanhados de nota fiscal e laudo de resistência (quando aplicável), em conformidade com as normas técnicas.'],
            ['question' => 'Posso solicitar bombeamento junto com o concreto?', 'answer' => 'Sim. Oferecemos serviço de bombeamento (bomba estacionária e lança) para lajes e estruturas em altura. O orçamento pode incluir concreto + bombeamento em uma única solicitação.'],
        ];
    }
@endphp
<section id="faq" class="border-b border-border bg-muted/30 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            @if(!empty($header['subtitle']))
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">{{ $header['subtitle'] }}</span>
            @endif
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">{{ $header['title'] ?? 'Perguntas frequentes' }}</h2>
            @if(!empty($header['description']))
                <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">{{ $header['description'] }}</p>
            @endif
        </div>

        <div class="mx-auto max-w-3xl space-y-3">
            @foreach($items as $item)
                <details class="group rounded-lg border border-border bg-card transition-colors hover:border-primary/30 [&[open]]:border-primary/40">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5 font-mono text-base font-semibold text-foreground">
                        {{ $item['question'] ?? '' }}
                        <i data-lucide="chevron-down" class="h-5 w-5 shrink-0 text-muted-foreground transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="border-t border-border px-6 pb-5 pt-2">
                        <p class="text-sm leading-relaxed text-muted-foreground">{{ $item['answer'] ?? '' }}</p>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
