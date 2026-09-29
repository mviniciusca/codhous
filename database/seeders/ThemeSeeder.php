<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'title' => 'Tema Moderno - Homepage',
                'slug' => 'tema-moderno/homepage',
                'is_visible' => true,
                'content' => [
                    ['type' => 'hero', 'data' => ['slides' => [['title' => 'Concreto do Futuro', 'description' => 'A tecnologia a favor da sua obra. Concreto usinado de alta precisão.', 'button_url' => '#orcamento', 'button_label' => 'Faça um Orçamento']], 'badge' => 'NOVIDADE', 'layout' => 'default', 'stats' => [['label' => 'Obras Inovadoras', 'value' => '500+'], ['label' => 'Projetos', 'value' => '99%']]]],
                    ['type' => 'services', 'data' => ['title' => 'Soluções Modernas', 'items' => [], 'description' => 'Serviços de ponta para o seu projeto com qualidade e eficiência máxima.']],
                    ['type' => 'showcase', 'data' => ['title' => 'Galeria', 'badge' => 'MODERNO', 'limit' => 6, 'description' => 'Confira nossas obras mais recentes com design inovador.']],
                    ['type' => 'contact_banner', 'data' => ['title' => 'Fale conosco', 'badge' => 'ONLINE', 'description' => 'Estamos 100% digitais. Atendimento rápido e fácil pelo seu canal preferido.', 'call_enabled' => true, 'email_enabled' => true, 'whatsapp_enabled' => true]],
                ]
            ],
            [
                'title' => 'Tema Corporativo - Homepage',
                'slug' => 'tema-corporativo/homepage',
                'is_visible' => true,
                'content' => [
                    ['type' => 'page_header', 'data' => ['badge' => 'CORPORATIVO', 'title' => 'Solidez e Confiança', 'description' => 'O parceiro ideal para grandes obras industriais e comerciais. Entregamos no prazo com segurança.', 'show_breadcrumbs' => false]],
                    ['type' => 'differentials', 'data' => ['title' => 'Nossos Pilares Corporativos', 'items' => [], 'subtitle' => 'Excelência', 'description' => 'Focamos na qualidade B2B e no rigor técnico.']],
                    ['type' => 'partners', 'data' => ['title' => 'Nossos Grandes Clientes', 'items' => [], 'description' => 'Conheça algumas das empresas que confiam na Codhous para suas grandes obras.']],
                    ['type' => 'timeline', 'data' => ['title' => 'Processo de Contratação B2B', 'steps' => []]],
                    ['type' => 'budget_form', 'data' => ['title' => 'Solicitar Proposta Comercial', 'description' => 'Preencha o formulário e um de nossos consultores comerciais entrará em contato.']],
                ]
            ],
            [
                'title' => 'Tema Criativo - Homepage',
                'slug' => 'tema-criativo/homepage',
                'is_visible' => true,
                'content' => [
                    ['type' => 'hero', 'data' => ['slides' => [['title' => 'Construindo Sonhos e Ideias', 'description' => 'Transformamos suas ideias mais ousadas em concreto. A base perfeita para a sua criatividade.', 'button_url' => '/contato', 'button_label' => 'Vamos conversar?']], 'badge' => 'CRIATIVIDADE', 'layout' => 'whatsapp', 'stats' => []]],
                    ['type' => 'showcase', 'data' => ['title' => 'Nossas Artes em Concreto', 'badge' => 'PORTFÓLIO', 'limit' => 8, 'description' => 'Inspire-se no que já construímos ao lado de grandes arquitetos e designers.']],
                    ['type' => 'testimonials', 'data' => ['title' => 'O que nossos clientes dizem', 'items' => []]],
                    ['type' => 'faq', 'data' => ['title' => 'Dúvidas? Nós ajudamos a esclarecer', 'items' => []]],
                    ['type' => 'cta_contact', 'data' => []],
                ]
            ]
        ];

        foreach ($themes as $theme) {
            Page::updateOrCreate(['slug' => $theme['slug']], $theme);
        }
    }
}
