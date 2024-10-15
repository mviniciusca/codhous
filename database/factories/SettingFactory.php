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
            /** Settings Default */
            'name' => 'Main',

            /** Application Settings */
            'app_name' => 'Codhous',
            'email' => 'test@test.dev',
            'office_hour' => 'Monday - Friday, 8AM - 5PM',
            'phone' => '2155554444',

            /** Meta Tags */
            'meta_title' => 'Codhous - Software Management for Websites',
            'meta_author' => 'Codhous Software',
            'meta_keywords' => 'software, laravel, filament',
            'meta_description' => 'Codhous is a software built with Laravel and Filament and it\'s ready to manager your entire website easily.',

            'whatsapp' => [
                'status' => true,
                'phone' => '',
                'color' => '#25d366',
                'icon' => 'logo-whatsapp',
                'message' => 'I need some help! It\'s from your website.',
            ],

            /** Budget Tool */
            'budget' => [
                'fck' => ['10', '15', '20', '25', '30', '35', '40', '45', '50'],
                'area' => ['Floor', 'Indoor', 'Street', 'School', 'House', 'Airport', 'Shopping'],
                'product' => ['Usinado', 'Bombeado'],
            ]
        ];
    }
}
