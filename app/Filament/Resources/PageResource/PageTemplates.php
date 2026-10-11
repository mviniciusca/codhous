<?php

namespace App\Filament\Resources\PageResource;

use App\Models\ContentSection;
use Filament\Forms\Components\Builder;

/**
 * Catálogo de templates do Page Builder.
 *
 * Cada template é uma lista de blocos `{type, data}` usando apenas campos que existem
 * no schema do bloco em PageResource (campos desconhecidos seriam descartados ao salvar).
 *
 * Blocos que só existem como seção global (hero, formulário de orçamento, calculadora,
 * formulário de contato) entram via `['module' => <tipo>]` e são resolvidos para um
 * `module_reference` apontando para a ContentSection ativa daquele tipo. Se a seção não
 * existir, usa-se o `fallback` (quando informado) ou o bloco é ignorado.
 */
class PageTemplates
{
    public const CATEGORIES = [
        'home' => 'Homepages',
        'institutional' => 'Institucional',
        'conversion' => 'Vendas & Conversão',
        'support' => 'Contato & Suporte',
    ];

    /**
     * @return array<string, array{label: string, description: string, category: string, icon: string, blocks: array<int, array<string, mixed>>}>
     */
    public static function all(): array
    {
        return [
            'home_completa' => [
                'label' => 'Home Completa',
                'description' => 'A vitrine clássica: hero, números, serviços, diferenciais, obras, depoimentos, FAQ e orçamento.',
                'category' => 'home',
                'icon' => 'heroicon-o-home',
                'blocks' => [
                    ['module' => ContentSection::TYPE_HERO],
                    self::statsBlock(),
                    self::servicesBlock(),
                    self::differentialsBlock(),
                    ['type' => 'showcase', 'data' => [
                        'badge' => 'NOSSAS OBRAS',
                        'title' => 'Projetos que saíram do papel',
                        'description' => 'Uma seleção de obras residenciais, comerciais e industriais atendidas pela nossa equipe.',
                        'limit' => 4,
                    ]],
                    self::testimonialsBlock(),
                    self::faqBlock(),
                    ['module' => ContentSection::TYPE_BUDGET_FORM],
                    ['type' => 'cta', 'data' => ['show_features' => true]],
                ],
            ],

            'home_conversao' => [
                'label' => 'Home Orçamento Rápido',
                'description' => 'Focada em conversão: orçamento e calculadora logo no início, condições de pagamento e prova social.',
                'category' => 'home',
                'icon' => 'heroicon-o-bolt',
                'blocks' => [
                    ['module' => ContentSection::TYPE_HERO],
                    ['module' => ContentSection::TYPE_BUDGET_FORM],
                    ['module' => ContentSection::TYPE_CALCULATOR],
                    self::timelineBlock(),
                    self::paymentOfferBlock(),
                    self::testimonialsBlock(),
                    self::contactBannerBlock(),
                ],
            ],

            'home_institucional' => [
                'label' => 'Home Institucional',
                'description' => 'Para reforçar marca e credibilidade: clientes, pilares, portfólio, frota, área de atendimento e depoimento em destaque.',
                'category' => 'home',
                'icon' => 'heroicon-o-building-office-2',
                'blocks' => [
                    ['module' => ContentSection::TYPE_HERO],
                    ['type' => 'partners', 'data' => [
                        'title' => 'Empresas que confiam no nosso trabalho',
                        'description' => 'Construtoras, incorporadoras e profissionais que constroem com a gente.',
                        'items' => [],
                    ]],
                    self::pillarsBlock(),
                    ['type' => 'showcase', 'data' => [
                        'badge' => 'PORTFÓLIO',
                        'title' => 'Obras realizadas',
                        'description' => 'Do pequeno reparo à grande estrutura, cada projeto recebe o mesmo cuidado técnico.',
                        'limit' => 6,
                    ]],
                    ['type' => 'equipment_showcase', 'data' => [
                        'badge' => 'FROTA PRÓPRIA',
                        'title' => 'Equipamentos prontos para a sua obra',
                        'description' => 'Caminhões betoneira, bombas e máquinas revisados e operados por equipe treinada.',
                    ]],
                    ['type' => 'coverage', 'data' => ['title' => 'Onde atuamos']],
                    self::featuredTestimonialBlock(),
                    ['type' => 'cta', 'data' => ['show_features' => true]],
                ],
            ],

            'sobre' => [
                'label' => 'Sobre a Empresa',
                'description' => 'Conte sua história: números, trajetória, missão/visão/valores, parceiros e um depoimento marcante.',
                'category' => 'institutional',
                'icon' => 'heroicon-o-identification',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'QUEM SOMOS',
                        'title' => 'Construindo confiança, obra após obra',
                        'description' => 'Conheça a história, as pessoas e os valores que sustentam o nosso trabalho.',
                        'show_breadcrumbs' => true,
                    ]],
                    self::statsBlock(),
                    ['type' => 'rich_text', 'data' => [
                        'content' => '<h2>Nossa história</h2>'
                            .'<p>Começamos com um caminhão, uma equipe pequena e um compromisso: entregar o material certo, no horário combinado. Com o tempo, a confiança dos clientes transformou esse compromisso em uma operação completa, com frota própria, controle tecnológico e atendimento próximo.</p>'
                            .'<p>Hoje atendemos de reformas residenciais a grandes empreendimentos, sempre com o mesmo cuidado técnico e a mesma transparência do primeiro dia.</p>'
                            .'<p><em>Substitua este texto pela história da sua empresa.</em></p>',
                    ]],
                    self::pillarsBlock(),
                    ['type' => 'timeline', 'data' => [
                        'title' => 'Nossa trajetória',
                        'steps' => [
                            ['step_label' => '2010', 'title' => 'Fundação', 'description' => 'Início das operações com foco no atendimento regional.', 'icon' => 'flag'],
                            ['step_label' => '2015', 'title' => 'Frota própria', 'description' => 'Ampliação da frota e da capacidade de entrega diária.', 'icon' => 'truck'],
                            ['step_label' => '2019', 'title' => 'Laboratório', 'description' => 'Controle tecnológico próprio para garantir cada traço.', 'icon' => 'flask-conical'],
                            ['step_label' => 'Hoje', 'title' => 'Referência na região', 'description' => 'Milhares de obras entregues e clientes que voltam sempre.', 'icon' => 'award'],
                        ],
                    ]],
                    ['type' => 'partners', 'data' => [
                        'title' => 'Quem constrói com a gente',
                        'description' => 'Parceiros e clientes que fazem parte da nossa história.',
                        'items' => [],
                    ]],
                    self::featuredTestimonialBlock(),
                    ['type' => 'cta', 'data' => ['show_features' => true]],
                ],
            ],

            'servicos' => [
                'label' => 'Serviços',
                'description' => 'Apresente o que você faz: cards de serviço com tópicos, processo de atendimento, diferenciais e FAQ.',
                'category' => 'institutional',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'O QUE FAZEMOS',
                        'title' => 'Soluções completas para a sua obra',
                        'description' => 'Fornecimento, bombeamento e locação de equipamentos com equipe técnica do início ao fim.',
                        'show_breadcrumbs' => true,
                    ]],
                    self::servicesBlock(),
                    self::timelineBlock(),
                    self::differentialsBlock(),
                    self::faqBlock(),
                    self::contactBannerBlock(),
                ],
            ],

            'portfolio' => [
                'label' => 'Portfólio de Obras',
                'description' => 'Galeria de projetos com números de resultado e um depoimento em destaque.',
                'category' => 'institutional',
                'icon' => 'heroicon-o-camera',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'PORTFÓLIO',
                        'title' => 'Nossas obras',
                        'description' => 'Conheça alguns dos projetos que ajudamos a construir.',
                        'show_breadcrumbs' => true,
                    ]],
                    self::statsBlock(),
                    ['type' => 'showcase', 'data' => [
                        'badge' => 'GALERIA',
                        'title' => 'Projetos realizados',
                        'description' => 'Residenciais, comerciais e industriais: cada obra com acompanhamento técnico.',
                        'limit' => 12,
                    ]],
                    self::featuredTestimonialBlock(),
                    ['type' => 'cta', 'data' => ['show_features' => true]],
                ],
            ],

            'equipamentos' => [
                'label' => 'Equipamentos & Locação',
                'description' => 'Catálogo de máquinas com vantagens da locação, condições de pagamento e dúvidas frequentes.',
                'category' => 'conversion',
                'icon' => 'heroicon-o-truck',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'LOCAÇÃO',
                        'title' => 'Equipamentos para cada etapa da obra',
                        'description' => 'Máquinas revisadas, entrega no canteiro e suporte técnico durante todo o contrato.',
                        'show_breadcrumbs' => true,
                    ]],
                    ['type' => 'equipment_showcase', 'data' => [
                        'badge' => 'CATÁLOGO',
                        'title' => 'Escolha o equipamento ideal',
                        'description' => 'Consulte disponibilidade e solicite seu orçamento direto pelo site.',
                    ]],
                    ['type' => 'cards', 'data' => [
                        'badge' => 'POR QUE ALUGAR',
                        'title' => 'Mais produtividade, menos custo fixo',
                        'description' => 'Use o equipamento certo só pelo tempo que precisar.',
                        'columns' => '4',
                        'items' => [
                            ['icon' => 'wrench', 'title' => 'Manutenção inclusa', 'description' => 'Revisão preventiva antes de cada locação.'],
                            ['icon' => 'truck', 'title' => 'Entrega no canteiro', 'description' => 'Levamos e retiramos o equipamento na sua obra.'],
                            ['icon' => 'calendar-clock', 'title' => 'Prazos flexíveis', 'description' => 'Diárias, semanas ou meses, conforme o cronograma.'],
                            ['icon' => 'headset', 'title' => 'Suporte técnico', 'description' => 'Equipe disponível para orientar a operação.'],
                        ],
                    ]],
                    self::paymentOfferBlock(),
                    ['type' => 'faq', 'data' => [
                        'title' => 'Dúvidas sobre locação',
                        'items' => [
                            ['question' => 'Qual o prazo mínimo de locação?', 'answer' => 'Trabalhamos com diárias, mas os melhores valores são para contratos semanais e mensais.'],
                            ['question' => 'O operador está incluso?', 'answer' => 'Depende do equipamento. Informe no pedido de orçamento se precisa de operador e incluímos na proposta.'],
                            ['question' => 'Quem paga o frete?', 'answer' => 'O frete é calculado pelo CEP da obra e aparece separado no orçamento.'],
                        ],
                    ]],
                    self::contactBannerBlock(),
                ],
            ],

            'landing_oferta' => [
                'label' => 'Landing Page de Oferta',
                'description' => 'Página de campanha: oferta em destaque, serviços, prova social e formulário de orçamento no final.',
                'category' => 'conversion',
                'icon' => 'heroicon-o-megaphone',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'OFERTA ESPECIAL',
                        'title' => 'Condições exclusivas para a sua obra',
                        'description' => 'Garanta seu orçamento com parcelamento facilitado e atendimento prioritário.',
                        'show_breadcrumbs' => false,
                    ]],
                    self::paymentOfferBlock(),
                    self::servicesBlock(),
                    self::differentialsBlock(),
                    self::testimonialsBlock(),
                    self::faqBlock(),
                    ['module' => ContentSection::TYPE_BUDGET_FORM, 'fallback' => self::contactBannerBlock()],
                ],
            ],

            'contato' => [
                'label' => 'Contato',
                'description' => 'Formulário com todos os canais (WhatsApp, telefone, e-mail), mapa, área de atendimento e FAQ.',
                'category' => 'support',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'CONTATO',
                        'title' => 'Fale com a nossa equipe',
                        'description' => 'Tire dúvidas, solicite um orçamento ou agende uma visita técnica. Respondemos rápido.',
                        'show_breadcrumbs' => true,
                    ]],
                    ['module' => ContentSection::TYPE_CTA_CONTACT, 'fallback' => self::contactBannerBlock()],
                    ['type' => 'map', 'data' => ['title' => 'Nossa localização']],
                    ['type' => 'coverage', 'data' => ['title' => 'Regiões atendidas']],
                    self::faqBlock(),
                ],
            ],

            'faq' => [
                'label' => 'Central de Dúvidas',
                'description' => 'Página dedicada a perguntas frequentes, com processo de atendimento e canais de contato.',
                'category' => 'support',
                'icon' => 'heroicon-o-question-mark-circle',
                'blocks' => [
                    ['type' => 'page_header', 'data' => [
                        'badge' => 'AJUDA',
                        'title' => 'Perguntas frequentes',
                        'description' => 'Reunimos as dúvidas mais comuns sobre pedidos, entregas e pagamentos.',
                        'show_breadcrumbs' => true,
                    ]],
                    ['type' => 'faq', 'data' => [
                        'title' => 'Pedidos e entregas',
                        'items' => [
                            ['question' => 'Com quanto tempo de antecedência devo pedir?', 'answer' => 'O ideal é agendar com 48h de antecedência para garantir o horário. Pedidos urgentes são avaliados conforme a disponibilidade da frota.'],
                            ['question' => 'Qual o volume mínimo?', 'answer' => 'O volume mínimo varia conforme o serviço e a região. Use a calculadora do site ou fale com a gente para confirmar.'],
                            ['question' => 'Vocês atendem a minha cidade?', 'answer' => 'Consulte a área de atendimento informando o CEP da obra. O frete é calculado automaticamente.'],
                            ['question' => 'Posso alterar o horário da entrega?', 'answer' => 'Sim, desde que avisado com antecedência. Entre em contato pelo WhatsApp com o número do pedido.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'title' => 'Pagamento e documentação',
                        'background_color' => 'bg-muted/30',
                        'items' => [
                            ['question' => 'Quais as formas de pagamento?', 'answer' => 'PIX, boleto e cartão de crédito em até 12x. Pagamentos à vista têm condições especiais.'],
                            ['question' => 'Recebo nota fiscal?', 'answer' => 'Sim. Todos os pedidos são acompanhados de nota fiscal e, quando aplicável, laudo técnico.'],
                            ['question' => 'O orçamento tem validade?', 'answer' => 'Sim, a validade é informada no PDF do orçamento enviado para você.'],
                        ],
                    ]],
                    self::timelineBlock(),
                    self::contactBannerBlock(),
                ],
            ],
        ];
    }

    public static function find(?string $key): ?array
    {
        return $key ? (self::all()[$key] ?? null) : null;
    }

    /**
     * Converte os blocos do template em blocos do Builder, resolvendo os módulos globais.
     *
     * @return array{blocks: array<int, array{type: string, data: array<string, mixed>}>, skipped: array<int, string>}
     */
    public static function resolve(array $template): array
    {
        $blocks = [];
        $skipped = [];
        $sectionIds = self::moduleSectionIds();

        foreach ($template['blocks'] as $block) {
            if (! isset($block['module'])) {
                $blocks[] = $block;

                continue;
            }

            $sectionId = $sectionIds[$block['module']] ?? null;

            if ($sectionId) {
                $blocks[] = ['type' => 'module_reference', 'data' => ['content_section_id' => $sectionId]];
            } elseif (isset($block['fallback'])) {
                $blocks[] = $block['fallback'];
            } else {
                $skipped[] = ContentSection::typeLabels()[$block['module']] ?? $block['module'];
            }
        }

        return ['blocks' => $blocks, 'skipped' => $skipped];
    }

    /**
     * Dados prontos para o seletor visual de templates.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function summaries(Builder $builder): array
    {
        $moduleLabels = ContentSection::typeLabels();
        $sectionIds = self::moduleSectionIds();

        return collect(self::all())
            ->map(function (array $template, string $key) use ($builder, $moduleLabels, $sectionIds): array {
                $blocks = collect($template['blocks'])->map(function (array $block) use ($builder, $moduleLabels, $sectionIds): array {
                    if (isset($block['module'])) {
                        $available = isset($sectionIds[$block['module']]);
                        $fallback = $block['fallback']['type'] ?? null;

                        return [
                            'label' => $available || ! $fallback
                                ? ($moduleLabels[$block['module']] ?? $block['module'])
                                : (string) $builder->getBlock($fallback)?->getLabel(),
                            'icon' => $available || ! $fallback
                                ? 'heroicon-o-squares-plus'
                                : ($builder->getBlock($fallback)?->getIcon() ?? 'heroicon-o-square-3-stack-3d'),
                            'global' => $available,
                            'missing' => ! $available && ! $fallback,
                        ];
                    }

                    $definition = $builder->getBlock($block['type']);

                    return [
                        'label' => (string) ($definition?->getLabel() ?? $block['type']),
                        'icon' => $definition?->getIcon() ?? 'heroicon-o-square-3-stack-3d',
                        'global' => false,
                        'missing' => false,
                    ];
                })->all();

                return [
                    'key' => $key,
                    'label' => $template['label'],
                    'description' => $template['description'],
                    'category' => $template['category'],
                    'icon' => $template['icon'],
                    'blocks' => $blocks,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * IDs das seções globais ativas, indexados por tipo (a primeira pela ordem de exibição).
     *
     * @return array<string, int>
     */
    protected static function moduleSectionIds(): array
    {
        return ContentSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'type'])
            ->unique('type')
            ->pluck('id', 'type')
            ->all();
    }

    // ───────────── Blocos reutilizados entre templates ─────────────

    protected static function statsBlock(): array
    {
        return ['type' => 'stats', 'data' => [
            'items' => [
                ['value' => '15+', 'label' => 'Anos de mercado', 'icon' => 'calendar-check'],
                ['value' => '2.500+', 'label' => 'Obras atendidas', 'icon' => 'building-2'],
                ['value' => '150 mil', 'label' => 'm³ entregues', 'icon' => 'truck'],
                ['value' => '98%', 'label' => 'Entregas no horário', 'icon' => 'clock'],
            ],
        ]];
    }

    protected static function servicesBlock(): array
    {
        return ['type' => 'services', 'data' => [
            'title' => 'Nossos serviços',
            'description' => 'Tudo o que a sua obra precisa, com qualidade técnica e entrega no prazo.',
            'items' => [
                [
                    'title' => 'Concreto Usinado',
                    'description' => 'Traços dosados em central, com controle tecnológico e entrega programada.',
                    'icon' => 'truck',
                    'bullets' => ['FCK de 15 a 50 MPa', 'Laudo de resistência', 'Entrega agendada'],
                    'cta_label' => 'Solicitar orçamento',
                    'cta_url' => '#orcamento',
                ],
                [
                    'title' => 'Bombeamento',
                    'description' => 'Bombas lança e estacionária para lajes, pilares e locais de difícil acesso.',
                    'icon' => 'arrow-up-circle',
                    'bullets' => ['Bomba lança', 'Bomba estacionária', 'Operadores treinados'],
                    'cta_label' => 'Solicitar orçamento',
                    'cta_url' => '#orcamento',
                ],
                [
                    'title' => 'Locação de Equipamentos',
                    'description' => 'Máquinas para terraplanagem, compactação e apoio à obra, com manutenção em dia.',
                    'icon' => 'tractor',
                    'bullets' => ['Frota revisada', 'Entrega no canteiro', 'Prazos flexíveis'],
                    'cta_label' => 'Consultar disponibilidade',
                    'cta_url' => '#orcamento',
                ],
            ],
        ]];
    }

    protected static function differentialsBlock(): array
    {
        return ['type' => 'differentials', 'data' => [
            'subtitle' => 'Por que nos escolher',
            'title' => 'Qualidade que dá para medir',
            'description' => 'Processos, tecnologia e gente experiente para que a sua obra não pare.',
            'background_color' => 'bg-muted/30',
            'items' => [
                ['icon' => 'clock', 'title' => 'Pontualidade', 'description' => 'Logística monitorada para chegar no horário combinado.'],
                ['icon' => 'flask-conical', 'title' => 'Controle tecnológico', 'description' => 'Cada carga é testada e acompanhada de laudo.'],
                ['icon' => 'headset', 'title' => 'Atendimento próximo', 'description' => 'Um consultor acompanha seu pedido do orçamento à entrega.'],
            ],
        ]];
    }

    protected static function pillarsBlock(): array
    {
        return ['type' => 'cards', 'data' => [
            'badge' => 'NOSSOS PILARES',
            'title' => 'O que nos move',
            'description' => 'Os princípios que orientam cada entrega.',
            'columns' => '3',
            'items' => [
                ['icon' => 'target', 'title' => 'Missão', 'description' => 'Fornecer soluções com agilidade, precisão e segurança, contribuindo para obras mais duráveis.'],
                ['icon' => 'eye', 'title' => 'Visão', 'description' => 'Ser a empresa mais confiável da região, reconhecida pela excelência técnica e pelo atendimento.'],
                ['icon' => 'shield-check', 'title' => 'Valores', 'description' => 'Ética, rigor técnico, pontualidade e respeito às pessoas e ao meio ambiente.'],
            ],
        ]];
    }

    protected static function timelineBlock(): array
    {
        return ['type' => 'timeline', 'data' => [
            'title' => 'Como funciona',
            'steps' => [
                ['step_label' => 'Etapa 1', 'title' => 'Solicite o orçamento', 'description' => 'Informe o CEP e os dados da obra pelo site ou WhatsApp.', 'icon' => 'message-square-text'],
                ['step_label' => 'Etapa 2', 'title' => 'Receba a proposta', 'description' => 'Enviamos o orçamento detalhado em PDF, com frete calculado.', 'icon' => 'file-text'],
                ['step_label' => 'Etapa 3', 'title' => 'Agende a entrega', 'description' => 'Escolha a data e o horário que melhor se encaixam no cronograma.', 'icon' => 'calendar-check'],
                ['step_label' => 'Etapa 4', 'title' => 'Receba na obra', 'description' => 'Equipe e equipamentos chegam no horário, prontos para trabalhar.', 'icon' => 'truck'],
            ],
        ]];
    }

    protected static function testimonialsBlock(): array
    {
        return ['type' => 'testimonials', 'data' => [
            'title' => 'O que dizem nossos clientes',
            'items' => [
                ['quote' => 'Atendimento rápido, material dentro do prazo e equipe técnica sempre disponível.', 'author_name' => 'Carlos Mendes', 'author_role' => 'Engenheiro civil', 'stars' => 5],
                ['quote' => 'Pontualidade e qualidade fazem toda a diferença. Viraram parceiros fixos das nossas obras.', 'author_name' => 'Ana Paula Costa', 'author_role' => 'Mestre de obras', 'stars' => 5],
                ['quote' => 'Orçamento claro, entrega no horário e um pós-venda excelente.', 'author_name' => 'Roberto Lima', 'author_role' => 'Arquiteto', 'stars' => 5],
            ],
        ]];
    }

    protected static function featuredTestimonialBlock(): array
    {
        return ['type' => 'featured_testimonial', 'data' => [
            'quote' => 'Em mais de dez obras juntos, nunca tivemos uma entrega fora do combinado. É o tipo de parceiro que deixa o cronograma tranquilo.',
            'author' => 'Mariana Ribeiro',
            'role' => 'Diretora de Engenharia',
        ]];
    }

    protected static function faqBlock(): array
    {
        return ['type' => 'faq', 'data' => [
            'title' => 'Perguntas frequentes',
            'items' => [
                ['question' => 'Em quanto tempo recebo o orçamento?', 'answer' => 'Em horário comercial respondemos em até 2 horas. Pelo site, a simulação é imediata.'],
                ['question' => 'Vocês atendem a minha região?', 'answer' => 'Informe o CEP da obra no orçamento: o sistema confirma a cobertura e calcula o frete.'],
                ['question' => 'Quais as formas de pagamento?', 'answer' => 'PIX, boleto e cartão de crédito em até 12x, com condições especiais à vista.'],
                ['question' => 'Recebo nota fiscal e laudo?', 'answer' => 'Sim. Todos os pedidos têm nota fiscal e, quando aplicável, laudo técnico.'],
            ],
        ]];
    }

    protected static function paymentOfferBlock(): array
    {
        return ['type' => 'payment_offer', 'data' => [
            'badge' => 'CONDIÇÃO ESPECIAL',
            'title' => 'Parcele em até 12x sem juros',
            'subtitle' => 'ou garanta desconto no pagamento à vista via PIX.',
            'button_label' => 'Fazer orçamento grátis',
        ]];
    }

    protected static function contactBannerBlock(): array
    {
        return ['type' => 'contact_banner', 'data' => [
            'badge' => 'ATENDIMENTO',
            'title' => 'Ficou com alguma dúvida?',
            'description' => 'Fale com a nossa equipe por WhatsApp, telefone ou e-mail. Respondemos rápido.',
            'whatsapp_enabled' => true,
            'call_enabled' => true,
            'email_enabled' => true,
        ]];
    }
}
