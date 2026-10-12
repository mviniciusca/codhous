@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'title' => null,
    'header' => null,
    'items' => null,
])
@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $section = \App\Models\ContentSection::getBySlug('faq');
    $header = $header ?: ($section?->content['header'] ?? []);
    $items = $items ?: ($section?->content['items'] ?? null);

    if (empty($items)) {
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

    $whatsappNumber = \App\Models\Setting::get('website.features.whatsapp_widget.number');
    $helpUrl = $whatsappNumber
        ? 'https://wa.me/55' . preg_replace('/\D/', '', $whatsappNumber) . '?text=' . urlencode('Olá! Tenho uma dúvida.')
        : '/contato';
    $centered = ($header['alignment'] ?? 'left') === 'center';
@endphp
@if(!\App\Models\ContentSection::isHidden('faq'))
<x-ui.section :data="$data" id="faq" background="bg-muted/30">
    <div @class(['grid gap-10', 'lg:grid-cols-12 lg:gap-16' => ! $centered])>
        {{-- Cabeçalho fixo ao lado das perguntas em telas largas --}}
        <div @class(['lg:col-span-5' => ! $centered])>
            <div @class(['lg:sticky lg:top-28' => ! $centered])>
                <x-ui.section-header
                    :header="$header"
                    :fallback-title="$header['title'] ?? 'Perguntas frequentes'"
                    :fallback-subtitle="$header['subtitle'] ?? null"
                    :fallback-description="$header['description'] ?? null"
                    :class="$centered ? '' : '!mb-6'"
                />
                <div @class(['flex items-center gap-3 rounded-card border border-dashed border-border p-4', 'mx-auto max-w-md' => $centered, 'hidden lg:flex' => ! $centered]) data-reveal>
                    <x-ui.icon-box icon="message-circle-question" :react="false" />
                    <div class="flex-1 text-sm">
                        <p class="font-semibold text-foreground">Não achou sua dúvida?</p>
                        <a href="{{ $helpUrl }}" target="_blank" rel="noopener" class="link-arrow">Fale com a gente <i data-lucide="arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div @class(['lg:col-span-7' => ! $centered, 'mx-auto w-full max-w-3xl' => $centered]) x-data="{ open: 0 }">
            <div class="divide-y divide-border/70 overflow-hidden rounded-card border border-border/70 bg-card">
                @foreach($items as $index => $item)
                    <div class="group" :class="open === {{ $index }} && 'bg-primary-500/[0.03]'" data-reveal style="--reveal-delay: {{ min($index, 5) * 50 }}ms">
                        <button type="button"
                                class="flex w-full items-center gap-4 px-5 py-4 text-left md:px-6 md:py-5"
                                @click="open = open === {{ $index }} ? null : {{ $index }}"
                                :aria-expanded="open === {{ $index }}">
                            <span class="font-mono text-xs font-semibold text-muted-foreground/50 transition-colors" :class="open === {{ $index }} && '!text-primary-ink'">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="flex-1 font-semibold text-foreground transition-colors group-hover:text-primary-ink">{{ $item['question'] ?? '' }}</span>
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-border transition-all duration-300"
                                  :class="open === {{ $index }} ? 'rotate-45 border-primary-500 bg-primary-500 text-primary-foreground' : 'text-muted-foreground'">
                                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                            </span>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse @if($index !== 0) x-cloak @endif>
                            <p class="px-5 pb-5 pl-[3.25rem] text-sm leading-relaxed text-muted-foreground md:px-6 md:pl-[3.75rem]">{{ $item['answer'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-ui.section>
@endif
