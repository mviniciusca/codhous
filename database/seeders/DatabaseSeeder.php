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
use App\Models\ProductOption;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'super_admin']);
        $user = User::factory()->create([
            'name'  => 'Codhous Software',
            'email' => 'codhous@codhous.app',
        ]);

        $user->assignRole($role);

        Setting::factory()
            ->has(Contact::factory())
            ->has(Module::factory())
            ->has(Layout::factory())
            ->has(Navigation::factory())
            ->has(CompanySetting::factory())
            ->has(Partner::factory()->count(5))
            ->create();

        // Criar produtos e opções de produtos
        $products = Product::factory(5)
            ->has(ProductOption::factory()
                ->count(3))
            ->create();

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
    }
}
