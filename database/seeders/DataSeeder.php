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
        // 1. Configurações da Empresa (Nota Fiscal, Endereço, etc)
        $this->seedSettings();

        // 2. Usuários Administrativos
        $this->seedAdmins();

        // 3. Outros dados estruturais
        $this->call([
            RolesAndPermissionsSeeder::class,
            PageSeeder::class,
            ContentSectionSeeder::class,
            OperationAreaSeeder::class,
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
                            'number' => '5511999999999',
                            'message' => 'Olá! Gostaria de um orçamento.',
                        ],
                    ],
                ],
                'company' => [
                    'document' => '00.000.000/0001-00',
                    'ie' => '000.000.000.000',
                    'im' => '000.000-0',
                    'trade_name' => 'Codhous',
                    'legal_name' => 'Codhous Soluções Tecnológicas LTDA',
                    'email' => 'contato@codhous.com.br',
                    'phone' => '(11) 99999-9999',
                    'address' => [
                        'street' => 'Rua das Inovações',
                        'number' => '123',
                        'neighborhood' => 'Parque Tecnológico',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'postcode' => '01234-567',
                    ],
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
                ],
            ],
        ]);
    }

    private function seedAdmins(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'codhous@codhous.app'],
            [
                'name' => 'Codhous Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Garantir que a role super_admin existe
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        // Atribuir permissões se necessário
        if ($role->permissions->isEmpty()) {
            $role->syncPermissions(Permission::all());
        }

        $admin->assignRole($role);
    }
}
