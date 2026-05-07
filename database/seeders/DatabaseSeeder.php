<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\CompanySetting;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Layout;
use App\Models\Location;
use App\Models\Mail;
use App\Models\Module;
use App\Models\Navigation;
use App\Models\Newsletter;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Carregar Dados Estruturais e Configurações via DataSeeder
        $this->call(DataSeeder::class);

        // 2. Criar dados de exemplo para desenvolvimento (opcional se for produção)
        
        // Criar produtos e opções de produtos específicos
        Product::factory()->concreto()->create();
        Product::factory()->polimento()->create();
        Product::factory()->maquinario()->create();

        // Criar localizações
        Location::factory(5)->create();

        // Criar orçamentos
        $budgets = Budget::factory(20)->create();

        // Criar o relacionamento entre orçamentos e produtos na tabela pivot
        foreach ($budgets as $budget) {
            // Pegar os dados dos produtos do array content
            $products_data = $budget->content['products'] ?? [];

            if (! empty($products_data)) {
                foreach ($products_data as $product_data) {
                    // Adicionar o produto ao orçamento na tabela pivot
                    $budget->products()->attach($product_data['product'], [
                        'product_option_id' => $product_data['product_option'] ?? null,
                        'location_id'       => $product_data['location'] ?? null,
                        'quantity'          => $product_data['quantity'] ?? 0,
                        'price'             => $product_data['price'] ?? 0,
                        'subtotal'          => $product_data['subtotal'] ?? 0,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }

            // Criar histórico para cada orçamento
            BudgetHistory::factory()
                ->state(function (array $attributes) {
                    return [
                        'user_id'   => 1, // Codhous Admin criado no DataSeeder
                    ];
                })
                ->create(['budget_id' => $budget->id]);
        }

        // Criar outros dados
        Newsletter::factory(20)->create();
        Mail::factory(20)->create();
        Customer::factory(10)->create();

        // Criar obras de exemplo para o portfólio
        $this->seedShowcases();
        
        $this->call([
            AlertSeeder::class,
            MachinerySeeder::class,
            ConcreteSeeder::class,
            LocationSeeder::class,
            FloorPolishingSeeder::class,
        ]);

        // 3. Criar Páginas Iniciais
        $this->createInitialPages();
    }

    protected function seedShowcases()
    {
        // Criar 3 obras de exemplo para o portfólio
        \App\Models\Showcase::create([
            'title' => 'Residencial Bela Vista',
            'description' => 'Fornecimento de 150m³ de concreto usinado para lajes e vigas com suporte técnico integral.',
            'location' => 'São Paulo, SP',
            'images' => ['https://images.unsplash.com/photo-1590059132718-5a8a9a1c6175?auto=format&fit=crop&q=80&w=800'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        \App\Models\Showcase::create([
            'title' => 'Pátio Industrial Logix',
            'description' => 'Execução de 2.000m² de piso de concreto polido de alta resistência para tráfego pesado.',
            'location' => 'Barueri, SP',
            'images' => ['https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&q=80&w=800'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        \App\Models\Showcase::create([
            'title' => 'Edifício Horizonte',
            'description' => 'Locação de bomba estacionária e fornecimento de concreto para bombeamento em grandes alturas.',
            'location' => 'Rio de Janeiro, RJ',
            'images' => ['https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=800'],
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    protected function createInitialPages()
    {
        // Página Inicial
        Page::updateOrCreate(['slug' => '/'], [
            'title' => 'Página Inicial',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 1,
            'content' => [
                ['type' => 'hero', 'data' => [
                    'layout' => 'default',
                    'badge' => 'Qualidade Certificada',
                    'slides' => [
                        ['title' => 'Concreto usinado com agilidade e precisão no traço', 'subtitle' => 'Entrega rápida e suporte técnico especializado.']
                    ],
                    'stats' => [
                        ['value' => '500+', 'label' => 'Obras atendidas'],
                        ['value' => '98%', 'label' => 'Pontualidade'],
                    ]
                ]],
                ['type' => 'partners', 'data' => ['title' => 'Nossos Parceiros']],
                ['type' => 'services', 'data' => ['title' => 'Nossos Serviços']],
                ['type' => 'cta_contact', 'data' => []],
                ['type' => 'showcase', 'data' => [
                    'badge' => 'PORTFÓLIO',
                    'title' => 'Nossas Obras',
                    'description' => 'Conheça alguns dos projetos realizados pela Codhous em toda a região.',
                    'limit' => 4
                ]],
                ['type' => 'calculator', 'data' => []],
                ['type' => 'budget_form', 'data' => [
                    'title' => 'Solicitar Orçamento Grátis',
                    'description' => 'Preencha os dados abaixo e receba uma proposta personalizada em até 24h.'
                ]],
            ]
        ]);

        // Serviços
        Page::updateOrCreate(['slug' => 'servicos'], [
            'title' => 'Serviços',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 2,
            'content' => [
                ['type' => 'services', 'data' => ['title' => 'Nossos Serviços']],
            ]
        ]);

        // Nossas Obras
        Page::updateOrCreate(['slug' => 'nossas-obras'], [
            'title' => 'Nossas Obras',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 3,
            'content' => [
                ['type' => 'page_header', 'data' => [
                    'badge' => 'PORTFÓLIO',
                    'title' => 'Nossas Obras',
                    'description' => 'Conheça alguns dos projetos realizados pela ConcretoPro em toda a região.',
                    'show_breadcrumbs' => true
                ]],
                ['type' => 'showcase', 'data' => ['title' => 'Portfólio de Obras', 'limit' => 10]],
            ]
        ]);

        // Sobre Nós
        Page::updateOrCreate(['slug' => 'sobre-nos'], [
            'title' => 'Sobre Nós',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 4,
            'content' => [
                ['type' => 'page_header', 'data' => [
                    'badge' => 'QUEM SOMOS',
                    'title' => 'Compromisso com a Solidez da sua Obra',
                    'description' => 'Desde a fundação até o acabamento, a Codhous é sua parceira em concreto usinado de alta performance.',
                    'show_breadcrumbs' => true,
                    'background_image' => 'about/betoneira-premium.png'
                ]],
                ['type' => 'rich_text', 'data' => ['content' => '<h2>Nossa História</h2><p>Com mais de 15 anos de atuação no mercado de construção civil, a Codhous nasceu com o propósito de desmistificar o fornecimento de concreto usinado. Entendemos que cada obra é única e que o cronograma é sagrado. Por isso, investimos em tecnologia de traço e logística de precisão para garantir que o seu projeto nunca pare.</p><p>Hoje, somos referência no Rio de Janeiro, atendendo desde pequenas reformas residenciais até grandes complexos industriais, sempre com o mesmo padrão de rigor técnico e atendimento personalizado.</p>']],
                ['type' => 'differentials', 'data' => [
                    'title' => 'Nossos Pilares',
                    'items' => [
                        [
                            'title' => 'Nossa Missão',
                            'description' => 'Fornecer soluções em concreto com agilidade, precisão e sustentabilidade, contribuindo para a segurança e durabilidade das construções de nossos clientes.',
                            'icon' => 'target'
                        ],
                        [
                            'title' => 'Nossa Visão',
                            'description' => 'Ser a concreteira mais confiável e inovadora do estado, reconhecida pela excelência técnica e pelo compromisso com o sucesso de cada obra.',
                            'icon' => 'eye'
                        ],
                        [
                            'title' => 'Nossos Valores',
                            'description' => 'Ética nas negociações, rigor técnico no traço, pontualidade britânica e respeito absoluto ao meio ambiente e às normas de segurança.',
                            'icon' => 'shield-check'
                        ],
                    ]
                ]],
                ['type' => 'timeline', 'data' => ['title' => 'Nossa Trajetória']],
            ]
        ]);

        // Contato
        Page::updateOrCreate(['slug' => 'contato'], [
            'title' => 'Contato',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 5,
            'content' => [
                ['type' => 'page_header', 'data' => [
                    'badge' => 'CONTATO',
                    'title' => 'Fale Conosco',
                    'description' => 'Tire suas dúvidas ou solicite uma visita técnica.',
                    'show_breadcrumbs' => true
                ]],
                ['type' => 'contact_form', 'data' => [
                    'title' => 'Envie sua Mensagem',
                    'description' => 'Preencha o formulário abaixo e retornaremos o mais breve possível.',
                    'email_to' => ''
                ]],
                ['type' => 'map', 'data' => [
                    'title' => 'Nossa Localização',
                    'iframe_code' => ''
                ]],
            ]
        ]);
    }
}
