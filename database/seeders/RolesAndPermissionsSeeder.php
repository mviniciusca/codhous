<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'user_view_any',
            'user_view',
            'user_create',
            'user_update',
            'user_delete',
            'user_force_delete',
            'user_restore',

            // Role permissions
            'role_view_any',
            'role_view',
            'role_create',
            'role_update',
            'role_delete',

            // Budget permissions
            'budget_view_any',
            'budget_view',
            'budget_create',
            'budget_update',
            'budget_delete',
            'budget_force_delete',
            'budget_restore',
            'budget_export',
            'budget_send_email',

            // Customer permissions
            'customer_view_any',
            'customer_view',
            'customer_create',
            'customer_update',
            'customer_delete',
            'customer_force_delete',
            'customer_restore',

            // Product permissions
            'product_view_any',
            'product_view',
            'product_create',
            'product_update',
            'product_delete',
            'product_force_delete',
            'product_restore',

            // Mail permissions
            'mail_view_any',
            'mail_view',
            'mail_create',
            'mail_update',
            'mail_delete',
            'mail_force_delete',
            'mail_restore',

            // Newsletter permissions
            'newsletter_view_any',
            'newsletter_view',
            'newsletter_create',
            'newsletter_update',
            'newsletter_delete',
            'newsletter_export',
            'newsletter_send',

            // Setting permissions
            'setting_view',
            'setting_update',
            'setting_budget',
            'setting_email',
            'setting_system',

            // Activity Log permissions
            'activity_log_view_any',
            'activity_log_view',

            // Report permissions
            'report_view',
            'report_export',

            // Page permissions
            'page_dashboard',
            'page_fabricator',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // 1. SUPER ADMIN - Full access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. ADMIN - Manager/Supervisor
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            // Users - View only
            'user_view_any',
            'user_view',

            // Budgets - Full except force delete
            'budget_view_any',
            'budget_view',
            'budget_create',
            'budget_update',
            'budget_delete',
            'budget_restore',
            'budget_export',
            'budget_send_email',

            // Customers - Full except force delete
            'customer_view_any',
            'customer_view',
            'customer_create',
            'customer_update',
            'customer_delete',
            'customer_restore',

            // Products - Full
            'product_view_any',
            'product_view',
            'product_create',
            'product_update',
            'product_delete',
            'product_force_delete',
            'product_restore',

            // Mail - Full except force delete
            'mail_view_any',
            'mail_view',
            'mail_create',
            'mail_update',
            'mail_delete',
            'mail_restore',

            // Newsletter - Full
            'newsletter_view_any',
            'newsletter_view',
            'newsletter_create',
            'newsletter_update',
            'newsletter_delete',
            'newsletter_export',
            'newsletter_send',

            // Settings - Partial
            'setting_view',
            'setting_update',
            'setting_budget',

            // Logs - Basic
            'activity_log_view_any',
            'activity_log_view',

            // Reports
            'report_view',
            'report_export',

            // Pages
            'page_dashboard',
            'page_fabricator',
        ]);

        // 3. VENDEDOR - Sales team
        $vendedor = Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);
        $vendedor->givePermissionTo([
            // Budgets - Own only
            'budget_view_any',
            'budget_view',
            'budget_create',
            'budget_update',
            'budget_delete',
            'budget_restore',
            'budget_export',
            'budget_send_email',

            // Customers - Create and edit
            'customer_view_any',
            'customer_view',
            'customer_create',
            'customer_update',

            // Products - View only
            'product_view_any',
            'product_view',

            // Mail - Own only, limited sending
            'mail_view_any',
            'mail_view',
            'mail_create',
            'mail_update',
            'mail_delete',
            'mail_restore',

            // Reports - Own only
            'report_view',
            'report_export',

            // Pages
            'page_dashboard',
        ]);

        // 4. FINANCEIRO - Financial team
        $financeiro = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => 'web']);
        $financeiro->givePermissionTo([
            // Budgets - View only
            'budget_view_any',
            'budget_view',
            'budget_export',

            // Customers - Basic view
            'customer_view_any',
            'customer_view',

            // Products - View only
            'product_view_any',
            'product_view',

            // Reports - Full
            'report_view',
            'report_export',

            // Pages
            'page_dashboard',
        ]);

        // 5. ATENDIMENTO - Customer service
        $atendimento = Role::firstOrCreate(['name' => 'atendimento', 'guard_name' => 'web']);
        $atendimento->givePermissionTo([
            // Budgets - Basic view only
            'budget_view_any',
            'budget_view',

            // Customers - Full except delete
            'customer_view_any',
            'customer_view',
            'customer_create',
            'customer_update',

            // Products - View only
            'product_view_any',
            'product_view',

            // Mail - Own messages
            'mail_view_any',
            'mail_view',
            'mail_create',
            'mail_update',

            // Newsletter - Manage subscribers
            'newsletter_view_any',
            'newsletter_view',
            'newsletter_create',
            'newsletter_update',

            // Pages
            'page_dashboard',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('');
        $this->command->info('Created roles:');
        $this->command->info('- super_admin (Full access)');
        $this->command->info('- admin (Manager/Supervisor)');
        $this->command->info('- vendedor (Sales team)');
        $this->command->info('- financeiro (Financial team)');
        $this->command->info('- atendimento (Customer service)');
    }
}
