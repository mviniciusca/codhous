<?php

namespace Database\Factories;

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
            'settings' => [
                'website' => [
                    'name' => 'Codhous',
                    'title' => 'Codhous - Soluções Digitais sob Medida',
                    'description' => 'Especialistas em desenvolvimento web de alta performance, design UX/UI e marketing digital para impulsionar o seu negócio.',
                    'navigation' => [
                        ['label' => 'Início', 'url' => '/'],
                        ['label' => 'Serviços', 'url' => '/servicos'],
                        ['label' => 'Sobre Nós', 'url' => '/sobre'],
                        ['label' => 'Contato', 'url' => '/contato'],
                    ],
                    'features' => [
                        'concrete_calculator' => true,
                        'budget_tool' => true,
                        'whatsapp_widget' => [
                            'enabled' => true,
                            'number' => '5500000000000',
                            'message' => 'Olá! Gostaria de saber mais sobre seus serviços.',
                        ],
                    ],
                    'homepage' => [
                        'slideshow' => [
                            ['image' => 'https://example.com/slide1.jpg', 'title' => 'Soluções Digitais', 'subtitle' => 'Transformamos sua ideia em realidade'],
                            ['image' => 'https://example.com/slide2.jpg', 'title' => 'Alta Performance', 'subtitle' => 'Websites rápidos e otimizados'],
                        ],
                    ],
                    'scripts' => [
                        'google_fonts' => 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@400;500;700&display=swap',
                        'head' => '<!-- Tag Manager or Analytics -->',
                        'footer' => '<!-- Custom Footer Scripts -->',
                    ],
                    'social_networks' => [
                        'instagram' => 'https://instagram.com/codhous',
                        'facebook' => 'https://facebook.com/codhous',
                        'linkedin' => 'https://linkedin.com/company/codhous',
                        'whatsapp' => 'https://wa.me/5500000000000',
                    ],
                ],
                'company' => [
                    'cnpj' => '00.000.000/0001-00',
                    'trade_name' => 'Codhous',
                    'legal_name' => 'Codhous Soluções Tecnológicas LTDA',
                    'email' => 'contato@codhous.com.br',
                    'phone' => '(00) 0000-0000',
                    'address' => [
                        'street' => 'Rua das Inovações',
                        'number' => '123',
                        'neighborhood' => 'Parque Tecnológico',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'zip_code' => '01234-567',
                    ],
                    'maps_link' => 'https://goo.gl/maps/example',
                    'opening_hours' => [
                        ['day' => 'Segunda-feira', 'hours' => '09:00 - 18:00'],
                        ['day' => 'Terça-feira', 'hours' => '09:00 - 18:00'],
                        ['day' => 'Quarta-feira', 'hours' => '09:00 - 18:00'],
                        ['day' => 'Quinta-feira', 'hours' => '09:00 - 18:00'],
                        ['day' => 'Sexta-feira', 'hours' => '09:00 - 18:00'],
                        ['day' => 'Sábado', 'hours' => '09:00 - 13:00'],
                        ['day' => 'Domingo', 'hours' => 'Fechado'],
                    ],
                ],
                'security' => [
                    'maintenance_mode' => false,
                    'maintenance_message' => 'Estamos realizando algumas melhorias. Voltaremos em instantes!',
                    'allowed_ips' => ['127.0.0.1'],
                ],
            ],
        ];
    }
}
