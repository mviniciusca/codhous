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
        // Criar o usuário super admin
        $user = User::factory()->create([
            'name'  => 'Codhous Software',
            'email' => 'codhous@codhous.app',
        ]);

        // Criar a role super_admin usando Filament Shield com guard 'web'
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Sincronizar todas as permissões existentes com a role super_admin
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());

        // Atribuir a role ao usuário
        $user->assignRole($role);

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
                ->state(function (array $attributes) use ($user, $budget) {
                    return [
                        'budget_id' => $budget->id,
                        'user_id'   => $user->id,
                    ];
                })
                ->create();
        }

        // Criar outros dados
        Newsletter::factory(20)->create();
        Mail::factory(20)->create();
        Customer::factory(10)->create();
        $this->call(SettingSeeder::class);
        
        // Criar as páginas iniciais
        $this->createInitialPages();
        
        $this->call(ContentSectionSeeder::class);
        $this->call(OperationAreaSeeder::class);
        $this->call(AlertSeeder::class);
    }

    protected function createInitialPages()
    {
        // Página Inicial
        Page::create([
            'title' => 'Página Inicial',
            'slug' => '/',
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
                ['type' => 'showcase', 'data' => ['title' => 'Nossas Obras', 'limit' => 4]],
                ['type' => 'cta', 'data' => ['title' => 'Pronto para começar?', 'button_label' => 'Solicitar Orçamento']],
            ]
        ]);

        // Serviços
        Page::create([
            'title' => 'Serviços',
            'slug' => 'servicos',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 2,
            'content' => [
                ['type' => 'services', 'data' => ['title' => 'Nossos Serviços']],
            ]
        ]);

        // Nossas Obras
        Page::create([
            'title' => 'Nossas Obras',
            'slug' => 'nossas-obras',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 3,
            'content' => [
                ['type' => 'showcase', 'data' => ['title' => 'Portfólio de Obras', 'limit' => 10]],
            ]
        ]);

        // Sobre Nós
        Page::create([
            'title' => 'Sobre Nós',
            'slug' => 'sobre-nos',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 4,
            'content' => [
                ['type' => 'rich_text', 'data' => ['content' => '<h2>Nossa História</h2><p>Há mais de 15 anos fornecendo concreto de alta qualidade...</p>']],
                ['type' => 'timeline', 'data' => ['title' => 'Nossa Trajetória']],
            ]
        ]);

        // Contato
        Page::create([
            'title' => 'Contato',
            'slug' => 'contato',
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 5,
            'content' => [
                ['type' => 'cta', 'data' => ['title' => 'Entre em Contato', 'subtitle' => 'Estamos prontos para atender seu projeto.']],
            ]
        ]);
    }
}
