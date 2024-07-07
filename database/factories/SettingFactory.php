<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'main',

            'app_name' => 'Codhous',
            'email' => 'test@test.dev',
            'office_hour' => 'Monday - Friday, 8AM - 5PM',
            'phone' => '2155554444',

            'meta_title' => 'Codhous - Software Management for Websites',
            'meta_author' => 'Codhous Software',
            'meta_keywords' => 'software, laravel, filament',
            'meta_description' => 'Codhous is a software built with Laravel and Filament and it\'s ready to manager your entire website easily.',

            'budget' => [
                'fck' => ["10", "20", "30"],
                'area' => ["Floor", "Indoor"],
                'type' => ["Usinado", "Bombeado"],
            ]
        ];
    }
}
