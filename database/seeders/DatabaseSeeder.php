<?php

namespace Database\Seeders;

use App\Models\Mail;
use App\Models\User;
use App\Models\Budget;
use App\Models\CompanySetting;
use App\Models\Layout;
use App\Models\Module;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Navigation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Newsletter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Codhous Software',
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

        Budget::factory(20)->create();
        Product::factory(3)->create();
        Newsletter::factory(10)->create();
        Mail::factory(20)->create();
        Customer::factory(10)->create();
    }
}
