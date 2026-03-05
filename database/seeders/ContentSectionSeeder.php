<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use Illuminate\Database\Seeder;

class ContentSectionSeeder extends Seeder
{
    /**
     * Cria as seções de conteúdo padrão para o site (slug única por tipo).
     * Os componentes usam fallback estático quando não há registro ativo.
     */
    public function run(): void
    {
        $sections = [
            [
                'slug' => 'hero',
                'type' => ContentSection::TYPE_HERO,
                'content' => [
                    'layout' => ContentSection::HERO_LAYOUT_DEFAULT,
                    'badge' => 'Qualidade Certificada',
                    'slideshow' => [
                        [
                            'title' => 'Concreto usinado com agilidade e precisão no traço',
                            'subtitle' => 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado para garantir o sucesso da sua obra do início ao fim.',
                        ],
                    ],
                    'stats' => [
                        ['value' => '500+', 'label' => 'Obras atendidas'],
                        ['value' => '98%', 'label' => 'Pontualidade'],
                        ['value' => '15+', 'label' => 'Anos de experiência'],
                    ],
                ],
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'partners',
                'type' => ContentSection::TYPE_PARTNERS,
                'content' => [
                    'header' => ['subtitle' => 'Empresas que confiam no nosso concreto'],
                    'items' => [
                        ['name' => 'MRV Engenharia', 'icon' => 'building-2'],
                        ['name' => 'Construtora Tenda', 'icon' => 'hard-hat'],
                        ['name' => 'Cyrela Brazil', 'icon' => 'landmark'],
                        ['name' => 'Gafisa S.A.', 'icon' => 'factory'],
                        ['name' => 'Even Construtora', 'icon' => 'warehouse'],
                        ['name' => 'Direcional Eng.', 'icon' => 'hammer'],
                        ['name' => 'Cury Construtora', 'icon' => 'construction'],
                        ['name' => 'Plano & Plano', 'icon' => 'ruler'],
                    ],
                ],
                'sort_order' => 1,
            ],
            [
                'slug' => 'services',
                'type' => ContentSection::TYPE_SERVICES,
                'content' => [
                    'header' => [
                        'subtitle' => 'Nossos Serviços',
                        'title' => 'Tudo que sua obra precisa em um só lugar',
                        'description' => 'Da fundação ao acabamento, oferecemos soluções completas com qualidade garantida.',
                    ],
                    'items' => [
                        ['title' => 'Concreto Usinado', 'subtitle' => 'por m³', 'description' => 'Concreto de alta qualidade com controle rigoroso de traço e resistência.', 'icon' => 'droplets', 'bullets' => ['FCK 20 a 50 MPa', 'Traço personalizado', 'Nota fiscal e certificado'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
                        ['title' => 'Bombeamento de Concreto', 'subtitle' => 'por serviço', 'description' => 'Serviço de bombeamento para lajes, fundações e estruturas em altura.', 'icon' => 'gauge', 'bullets' => ['Bomba estacionária', 'Bomba lança', 'Até 40m de altura'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
                        ['title' => 'Locação de Máquinas', 'subtitle' => 'diária / hora', 'description' => 'Equipamentos de ponta para sua obra.', 'icon' => 'wrench', 'bullets' => ['Retroescavadeira', 'Minicarregadeira', 'Compactador de solo'], 'cta_label' => 'Solicitar Orçamento', 'cta_url' => '#orcamento'],
                    ],
                ],
                'sort_order' => 2,
            ],
            [
                'slug' => 'timeline',
                'type' => ContentSection::TYPE_TIMELINE,
                'content' => [
                    'header' => ['subtitle' => 'Processo Simplificado', 'title' => 'Como funciona', 'description' => 'Do orçamento à entrega, tudo pensado para facilitar sua obra.'],
                    'steps' => [
                        ['step_label' => 'Etapa 1', 'title' => 'Solicite o Orçamento', 'description' => 'Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra.', 'icon' => 'message-square-text'],
                        ['step_label' => 'Etapa 2', 'title' => 'Aprovação do Traço', 'description' => 'Nossos engenheiros definem o traço ideal para a necessidade e o tipo da sua estrutura.', 'icon' => 'clipboard-check'],
                        ['step_label' => 'Etapa 3', 'title' => 'Produção na Usina', 'description' => 'O concreto é produzido com controle de qualidade rigoroso e rastreabilidade total.', 'icon' => 'factory'],
                        ['step_label' => 'Etapa 4', 'title' => 'Entrega na Obra', 'description' => 'Frota própria com rastreamento entrega o concreto no horário combinado.', 'icon' => 'truck'],
                    ],
                ],
                'sort_order' => 3,
            ],
            [
                'slug' => 'faq',
                'type' => ContentSection::TYPE_FAQ,
                'content' => [
                    'header' => ['subtitle' => 'Dúvidas Frequentes', 'title' => 'Perguntas frequentes', 'description' => 'Respostas rápidas sobre concreto usinado, entrega e orçamento.'],
                    'items' => [
                        ['question' => 'Qual o prazo para receber o orçamento?', 'answer' => 'Em horário comercial, respondemos em até 2 horas.'],
                        ['question' => 'Vocês entregam no dia que eu precisar?', 'answer' => 'Sim. Trabalhamos com agendamento e nossa logística garante alta taxa de pontualidade.'],
                        ['question' => 'Qual o volume mínimo de concreto?', 'answer' => 'O volume mínimo varia conforme a região. Entre em contato ou use a calculadora no site.'],
                        ['question' => 'O concreto vem com nota fiscal e certificado?', 'answer' => 'Sim. Todos os carregamentos são acompanhados de nota fiscal e laudo de resistência.'],
                        ['question' => 'Posso solicitar bombeamento junto com o concreto?', 'answer' => 'Sim. Oferecemos serviço de bombeamento. O orçamento pode incluir concreto + bombeamento.'],
                    ],
                ],
                'sort_order' => 4,
            ],
            [
                'slug' => 'testimonials',
                'type' => ContentSection::TYPE_TESTIMONIALS,
                'content' => [
                    'header' => ['subtitle' => 'Depoimentos', 'title' => 'O que dizem nossos clientes', 'description' => 'Empresas e obras que confiam na nossa entrega e no nosso suporte.'],
                    'items' => [
                        ['quote' => '"Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível."', 'author_name' => 'Carlos Mendes', 'author_role' => 'Engenheiro', 'stars' => 5],
                        ['quote' => '"Pontualidade e qualidade do traço fazem a diferença."', 'author_name' => 'Ana Paula Costa', 'author_role' => 'Mestre de obras', 'stars' => 5],
                        ['quote' => '"Orçamento claro, entrega no horário e suporte pós-venda."', 'author_name' => 'Roberto Lima', 'author_role' => 'Arquiteto', 'stars' => 5],
                    ],
                ],
                'sort_order' => 5,
            ],
            [
                'slug' => 'coverage',
                'type' => ContentSection::TYPE_COVERAGE,
                'content' => [
                    'header' => ['subtitle' => 'Cobertura', 'title' => 'Onde atendemos', 'description' => 'Atuamos na região com frota própria e logística integrada.'],
                    'cities' => [
                        ['label' => 'São Paulo (capital e região metropolitana)'],
                        ['label' => 'Guarulhos e ABC Paulista'],
                        ['label' => 'Osasco, Barueri e região Oeste'],
                        ['label' => 'Campinas e região'],
                        ['label' => 'Santos e Baixada Santista'],
                        ['label' => 'Outras regiões sob consulta'],
                    ],
                    'sidebar' => [
                        ['title' => 'Raio de entrega', 'description' => 'Consulte disponibilidade e prazo para sua cidade no orçamento.'],
                        ['title' => 'Frota própria', 'description' => 'Rastreamento e pontualidade em todas as entregas.'],
                    ],
                ],
                'sort_order' => 6,
            ],
            [
                'slug' => 'differentials',
                'type' => ContentSection::TYPE_DIFFERENTIALS,
                'content' => [
                    'header' => ['subtitle' => 'Por que nos escolher', 'title' => 'Excelência técnica em cada carregamento', 'description' => 'Estamos redefinindo o padrão de atendimento no mercado de concreto usinado.'],
                    'items' => [
                        ['icon' => 'clock', 'title' => 'Zero Atrasos', 'description' => 'Nosso sistema de logística GPS garante que seu concreto chegue no horário programado.'],
                        ['icon' => 'microscope', 'title' => 'Laboratório', 'description' => 'Testamos rigorosamente cada lote em nosso laboratório próprio seguindo as normas NBR.'],
                        ['icon' => 'smartphone', 'title' => 'App de Gestão', 'description' => 'Acompanhe seu pedido e gerencie seus orçamentos pelo smartphone.'],
                        ['icon' => 'award', 'title' => 'Selo Verde', 'description' => 'Comprometidos com a sustentabilidade através do reuso de água e gestão de resíduos.'],
                    ],
                ],
                'sort_order' => 7,
            ],
            [
                'slug' => 'cta_contact',
                'type' => ContentSection::TYPE_CTA_CONTACT,
                'content' => [
                    'title' => 'Fale conosco',
                    'subtitle' => 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.',
                ],
                'sort_order' => 8,
            ],
        ];

        foreach ($sections as $data) {
            ContentSection::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => ContentSection::typeLabels()[$data['type']] ?? $data['type'],
                    'type' => $data['type'],
                    'content' => $data['content'],
                    'is_active' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}
