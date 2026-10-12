<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que existe apenas um registro de configurações e o ID é sempre 1
        Setting::truncate();
        Setting::factory()->create(['id' => 1]);
    }
}
