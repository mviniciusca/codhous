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
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductOption;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Codhous Software',
            'email' => 'codhous@codhous.app',
        ]);

        Setting::factory()
            ->has(Contact::factory())
            ->has(Module::factory())
            ->has(Layout::factory())
            ->has(Navigation::factory())
            ->has(CompanySetting::factory())
            ->has(Partner::factory()->count(5))
            ->create();

        Product::factory(5)
            ->has(ProductOption::factory()
                ->count(3))
            ->create();

        Location::factory(5)->create();
        Budget::factory(100)
            ->has(
                BudgetHistory::factory()
                    ->state(function (array $attributes) use ($user) {
                        return [
                            'user_id' => $user->id,
                        ];
                    })
            )
            ->create();
        Newsletter::factory(100)->create();
        Mail::factory(100)->create();
        Customer::factory(10)->create();
    }
}
