<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Mail;
use App\Models\Module;
use App\Models\Partner;
use App\Models\User;
use App\Models\Layout;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Navigation;
use App\Models\Newsletter;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $setting = Setting::factory()
            ->create();

        Contact::factory()->create([
            'setting_id' => $setting->id
        ]);

        Module::factory()->create([
            'setting_id' => $setting->id
        ]);

        Layout::factory()->create([
            'setting_id' => $setting->id,
        ]);

        Navigation::factory()->create([
            'setting_id' => $setting->id,
        ]);

        User::factory()->create([
            'name' => 'Codhous Software',
            'email' => 'codhous@codhous.app',
        ]);

        Partner::factory(20)->create([
            'setting_id' => $setting->id,
        ]);

        Budget::factory(100)->create();

        Newsletter::factory(10)->create();
        Mail::factory(143)->create();
        Customer::factory(36)->create();

        // DB::table('pages')
        //     ->create([]);

    }
}
