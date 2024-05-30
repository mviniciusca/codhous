<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use App\Models\Layout;
use App\Models\Setting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        Layout::factory()->create([
            'setting_id' => $setting->id,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
