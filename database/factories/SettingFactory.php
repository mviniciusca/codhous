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
                        ['label' => 'Início',       'url' => '/'],
                        ['label' => 'Serviços',      'url' => '/servicos'],
                        ['label' => 'Nossas Obras',  'url' => '/nossas-obras'],
                        ['label' => 'Sobre Nós',     'url' => '/sobre-nos'],
                        ['label' => 'Contato',       'url' => '/contato'],
                    ],
                    'features' => [
                        'concrete_calculator' => true,
                        'budget_tool' => true,
                        'whatsapp_widget' => [
                            'enabled' => true,
                            'number' => '(21) 90000-0000',
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
                        'instagram' => 'https://instagram.com/codhoussoftware',
                        'facebook' => 'https://facebook.com/codhoussoftware',
                        'linkedin' => 'https://linkedin.com/company/codhoussoftware',
                        'twitter' => 'https://twitter.com/codhoussoftware',
                        'whatsapp' => 'https://wa.me/5521900000000',
                    ],
                ],
                'company' => [
                    'document' => '00.000.000/0001-00',
                    'ie' => '000.000.000.000',
                    'im' => '000.000-0',
                    'trade_name' => 'Codhous',
                    'legal_name' => 'Codhous Soluções Tecnológicas LTDA',
                    'email' => 'contato@codhous.com.br',
                    'phone' => '(21) 90000-0000',
                    'address' => [
                        'street' => 'Rua das Inovações',
                        'number' => '123',
                        'neighborhood' => 'Parque Tecnológico',
                        'city' => 'Rio de Janeiro',
                        'state' => 'RJ',
                        'postcode' => '20000-000',
                    ],
                    'maps_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29422.638814103528!2d-43.23815695!3d-22.80876775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x99799368903d71%3A0xead21443a9686abf!2sGale%C3%A3o%2C%20Rio%20de%20Janeiro%20-%20RJ!5e0!3m2!1spt-BR!2sbr!4v1778116472539!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                    'opening_hours' => 'Segunda a Sexta, 08:00 às 18:00',
                    'budget_information' => 'Pagamento em até 10x sem juros no cartão de crédito. Entrega em 5 dias úteis.',
                ],
                'security' => [
                    'maintenance_mode' => false,
                    'maintenance_message' => 'Estamos realizando algumas melhorias. Voltaremos em instantes!',
                    'allowed_ips' => [],
                ],
                'layout' => [
                    'logo' => 'logos/logo.png',
                ],
            ],
        ];
    }
}
