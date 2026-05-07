<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use App\Models\OperationArea;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Seed the application's database with the current site content.
     */
    public function run(): void
    {
        // 1. Áreas de Operação
        $areas = [
            ['city' => 'Rio de Janeiro', 'state' => 'RJ', 'postcode_prefix' => '20000', 'postcode_start' => '20000', 'postcode_end' => '23999', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '0.00'],
            ['city' => 'Niterói', 'state' => 'RJ', 'postcode_prefix' => '24000', 'postcode_start' => '24000', 'postcode_end' => '24399', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '80.00'],
            ['city' => 'São Gonçalo', 'state' => 'RJ', 'postcode_prefix' => '24400', 'postcode_start' => '24400', 'postcode_end' => '24799', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '120.00'],
            ['city' => 'Duque de Caxias', 'state' => 'RJ', 'postcode_prefix' => '25000', 'postcode_start' => '25000', 'postcode_end' => '25299', 'is_active' => true, 'is_base' => true, 'shipping_fee' => '0.00'],
            ['city' => 'São João de Meriti', 'state' => 'RJ', 'postcode_prefix' => '25500', 'postcode_start' => '25500', 'postcode_end' => '25699', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '50.00'],
            ['city' => 'Magé', 'state' => 'RJ', 'postcode_prefix' => '25900', 'postcode_start' => '25900', 'postcode_end' => '25999', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '90.00'],
            ['city' => 'Nova Iguaçu', 'state' => 'RJ', 'postcode_prefix' => '26000', 'postcode_start' => '26000', 'postcode_end' => '26399', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '70.00'],
            ['city' => 'Belford Roxo', 'state' => 'RJ', 'postcode_prefix' => '26100', 'postcode_start' => '26100', 'postcode_end' => '26299', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '60.00'],
            ['city' => 'Nilópolis', 'state' => 'RJ', 'postcode_prefix' => '26500', 'postcode_start' => '26500', 'postcode_end' => '26599', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '65.00'],
            ['city' => 'Mesquita', 'state' => 'RJ', 'postcode_prefix' => '26550', 'postcode_start' => '26550', 'postcode_end' => '26599', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '60.00'],
            ['city' => 'Queimados', 'state' => 'RJ', 'postcode_prefix' => '26320', 'postcode_start' => '26320', 'postcode_end' => '26399', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '85.00'],
            ['city' => 'Itaguaí', 'state' => 'RJ', 'postcode_prefix' => '23800', 'postcode_start' => '23800', 'postcode_end' => '23899', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '150.00'],
            ['city' => 'Maricá', 'state' => 'RJ', 'postcode_prefix' => '24900', 'postcode_start' => '24900', 'postcode_end' => '24999', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '130.00'],
            ['city' => 'Guapimirim', 'state' => 'RJ', 'postcode_prefix' => '25940', 'postcode_start' => '25940', 'postcode_end' => '25949', 'is_active' => true, 'is_base' => false, 'shipping_fee' => '100.00'],
        ];

        foreach ($areas as $area) {
            OperationArea::updateOrCreate(['city' => $area['city']], $area);
        }

        // 2. Seções Globais (ContentSections)
        $sections = [
            [
                'slug' => 'differentials',
                'name' => 'Diferenciais',
                'type' => 'differentials',
                'content' => [
                    'items' => [
                        ['icon' => 'clock', 'title' => 'Zero Atrasos', 'description' => 'Nosso sistema de logística GPS garante que seu concreto chegue no horário programado.'],
                        ['icon' => 'microscope', 'title' => 'Laboratório', 'description' => 'Testamos rigorosamente cada lote em nosso laboratório próprio seguindo as normas NBR.'],
                        ['icon' => 'smartphone', 'title' => 'App de Gestão', 'description' => 'Acompanhe seu pedido e gerencie seus orçamentos pelo smartphone.'],
                        ['icon' => 'award', 'title' => 'Selo Verde', 'description' => 'Comprometidos com a sustentabilidade através do reuso de água e gestão de resíduos.'],
                    ],
                    'header' => [
                        'title' => 'Excelência técnica em cada carregamento',
                        'subtitle' => 'Por que nos escolher',
                        'description' => 'Estamos redefinindo o padrão de atendimento no mercado de concreto usinado.'
                    ]
                ]
            ],
            [
                'slug' => 'cta_contact',
                'name' => 'CTA Contato',
                'type' => 'cta_contact',
                'content' => [
                    'title' => 'Fale conosco',
                    'subtitle' => 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.'
                ]
            ]
        ];

        foreach ($sections as $sec) {
            ContentSection::updateOrCreate(['slug' => $sec['slug']], array_merge($sec, ['is_active' => true]));
        }

        // 3. Páginas Dinâmicas
        $pages = [
            [
                'title' => 'Página Inicial',
                'slug' => '/',
                'content' => [
                    ['type' => 'hero', 'data' => ['slides' => [['title' => 'Concreto Usinado com Logística de Precisão', 'description' => 'Entregamos o concreto certo, no traço exato e no horário programado para sua obra não parar.', 'button_url' => '#orcamento', 'button_label' => 'Solicitar Orçamento', 'background_image' => 'hero/betoneira-rio.png']], 'badge' => 'Referência no Rio de Janeiro', 'layout' => 'default', 'stats' => [['label' => 'Obras Entregues', 'value' => '2.500+'], ['label' => 'm³ de Concreto', 'value' => '150k+'], ['label' => 'Anos de Mercado', 'value' => '15+']]]],
                    ['type' => 'partners', 'data' => ['title' => 'Nossos Parceiros', 'items' => [], 'description' => null]],
                    ['type' => 'differentials', 'data' => ['title' => 'Nossos Pilares', 'items' => [], 'subtitle' => null, 'description' => null]],
                    ['type' => 'services', 'data' => ['title' => 'Nossos Serviços', 'items' => [], 'description' => null]],
                    ['type' => 'showcase', 'data' => ['title' => 'Nossas Obras', 'badge' => 'PORTFÓLIO', 'limit' => 4, 'description' => 'Conheça alguns dos projetos realizados pela Codhous em toda a região.']],
                    ['type' => 'calculator', 'data' => ['title' => null]],
                    ['type' => 'budget_form', 'data' => ['title' => 'Solicitar Orçamento Grátis', 'description' => 'Preencha os dados abaixo e receba uma proposta personalizada em até 24h.']],
                    ['type' => 'testimonials', 'data' => ['title' => null, 'items' => []]],
                    ['type' => 'cta_contact', 'data' => []],
                    ['type' => 'faq', 'data' => ['title' => null, 'items' => []]]
                ]
            ],
            [
                'title' => 'Serviços',
                'slug' => 'servicos',
                'content' => [
                    ['type' => 'services', 'data' => ['title' => null, 'items' => [], 'description' => null]],
                    ['type' => 'timeline', 'data' => ['title' => null, 'steps' => []]],
                    ['type' => 'testimonials', 'data' => ['title' => null, 'items' => []]],
                    ['type' => 'contact_banner', 'data' => ['title' => 'Fale conosco', 'badge' => 'ATENDIMENTO', 'description' => 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.', 'call_enabled' => true, 'email_enabled' => true, 'whatsapp_enabled' => true]]
                ]
            ],
            [
                'title' => 'Nossas Obras',
                'slug' => 'nossas-obras',
                'content' => [
                    ['type' => 'page_header', 'data' => ['badge' => 'PORTFÓLIO', 'title' => 'Nossas Obras', 'description' => 'Conheça alguns dos projetos realizados pela ConcretoPro em toda a região.', 'show_breadcrumbs' => true]],
                    ['type' => 'showcase', 'data' => ['limit' => 10, 'title' => 'Portfólio de Obras']]
                ]
            ],
            [
                'title' => 'Sobre Nós',
                'slug' => 'sobre-nos',
                'content' => [
                    ['type' => 'page_header', 'data' => ['badge' => 'QUEM SOMOS', 'title' => 'Compromisso com a Solidez da sua Obra', 'description' => 'Desde a fundação até o acabamento, a Codhous é sua parceira em concreto usinado de alta performance.', 'background_image' => 'about/betoneira-premium.png', 'show_breadcrumbs' => true]],
                    ['type' => 'rich_text', 'data' => ['content' => '<h2>Nossa História</h2><p>Com mais de 15 anos de atuação no mercado de construção civil, a Codhous nasceu com o propósito de desmistificar o fornecimento de concreto usinado. Entendemos que cada obra é única e que o cronograma é sagrado. Por isso, investimos em tecnologia de traço e logística de precisão para garantir que o seu projeto nunca pare.</p><p>Hoje, somos referência no Rio de Janeiro, atendendo desde pequenas reformas residenciais até grandes complexos industriais, sempre com o mesmo padrão de rigor técnico e atendimento personalizado.</p>']],
                    ['type' => 'differentials', 'data' => ['title' => 'Nossos Pilares', 'items' => [['icon' => 'target', 'title' => 'Nossa Missão', 'description' => 'Fornecer soluções em concreto com agilidade, precisão e sustentabilidade, contribuindo para a segurança e durabilidade das construções de nossos clientes.'], ['icon' => 'eye', 'title' => 'Nossa Visão', 'description' => 'Ser a concreteira mais confiável e inovadora do estado, reconhecida pela excelência técnica e pelo compromisso com o sucesso de cada obra.'], ['icon' => 'shield-check', 'title' => 'Nossos Valores', 'description' => 'Ética nas negociações, rigor técnico no traço, pontualidade britânica e respeito absoluto ao meio ambiente e às normas de segurança.']], 'subtitle' => null, 'description' => null]]
                ]
            ],
            [
                'title' => 'Contato',
                'slug' => 'contato',
                'content' => [
                    ['type' => 'page_header', 'data' => ['badge' => 'CONTATO', 'title' => 'Fale Conosco', 'description' => 'Tire suas dúvidas ou solicite uma visita técnica.', 'show_breadcrumbs' => true]],
                    ['type' => 'faq', 'data' => ['title' => null, 'items' => []]],
                    ['type' => 'contact_form', 'data' => ['title' => 'Envie sua Mensagem', 'description' => 'Preencha o formulário abaixo e retornaremos o mais breve possível.']],
                    ['type' => 'map', 'data' => ['title' => null, 'iframe_code' => null]]
                ]
            ]
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['is_visible' => true]));
        }
    }
}
