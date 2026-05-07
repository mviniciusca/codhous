<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Configurações da Empresa
        $this->seedSettings();

        // 2. Usuários Administrativos
        $this->seedAdmins();

        // 3. Conteúdo do Site (Páginas, Seções e Áreas)
        $this->call([
            ContentSeeder::class,
        ]);
    }

    private function seedSettings(): void
    {
        Setting::query()->delete();
        Setting::create([
            'id' => 1,
            'settings' => [
                'website' => [
                    'name' => 'Codhous',
                    'title' => 'Codhous - Soluções Digitais sob Medida',
                    'description' => 'Especialistas em desenvolvimento web de alta performance.',
                    'navigation' => [
                        ['label' => 'Início', 'url' => '/'],
                        ['label' => 'Serviços', 'url' => '/servicos'],
                        ['label' => 'Nossas Obras', 'url' => '/nossas-obras'],
                        ['label' => 'Sobre Nós', 'url' => '/sobre-nos'],
                        ['label' => 'Contato', 'url' => '/contato'],
                    ],
                    'features' => [
                        'concrete_calculator' => true,
                        'budget_tool' => true,
                        'whatsapp_widget' => [
                            'enabled' => true,
                            'number' => '21900000000',
                            'message' => 'Olá! Gostaria de um orçamento.',
                        ],
                    ],
                    'social_networks' => [
                        'instagram' => 'codhoussoftware',
                        'facebook' => 'codhoussoftware',
                        'linkedin' => 'codhoussoftware',
                        'twitter' => 'codhoussoftware',
                        'whatsapp' => 'https://wa.me/5521900000000',
                    ],
                    'scripts' => [
                        'google_font_family' => 'Jost',
                        'head' => '<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">',
                        'footer' => '',
                    ],
                ],
                'company' => [
                    'document' => '00.000.000/0001-00',
                    'ie' => '000.000.000.000',
                    'im' => '000.000-0',
                    'trade_name' => 'Codhous',
                    'legal_name' => 'Codhous Soluções Tecnológicas LTDA',
                    'email' => 'contato@codhous.com.br',
                    'phone' => '(21) 90000-0000',
                    'address' => [
                        'street' => 'Rua das Inovações',
                        'number' => '123',
                        'neighborhood' => 'Parque Tecnológico',
                        'city' => 'Rio de Janeiro',
                        'state' => 'RJ',
                        'postcode' => '20000-000',
                    ],
                    'maps_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29422.638814103528!2d-43.23815695!3d-22.80876775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x99799368903d71%3A0xead21443a9686abf!2sGale%C3%A3o%2C%20Rio%20de%20Janeiro%20-%20RJ!5e0!3m2!1spt-BR!2sbr!4v1778116472539!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                    'opening_hours' => 'Segunda a Sexta, 08:00 às 18:00',
                    'budget_information' => "• Validade da proposta: 15 dias.\n• Pagamento: 50% na aprovação e 50% na entrega.\n• Documento gerado eletronicamente.",
                ],
                'layout' => [
                    'logo' => 'logos/logo.png',
                    'primary_color' => '#000000',
                    'secondary_color' => '#ffffff',
                ],
                'security' => [
                    'maintenance_mode' => false,
                    'turnstile' => [
                        'enabled' => true,
                        'site_key' => '1x00000000000000000000AA',
                        'secret_key' => '1x0000000000000000000000000000000AA',
                    ],
                ],
            ],
        ]);
    }

    private function seedAdmins(): void
    {
        User::updateOrCreate(
            ['email' => 'codhous@codhous.app'],
            [
                'name' => 'Codhous Admin',
                'password' => bcrypt('password'),
            ]
        );
    }
}
