<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pages')->delete();
        
        \DB::table('pages')->insert(array (
            0 => 
            array (
                'id' => 2,
                'title' => 'Serviços',
                'slug' => 'servicos',
                'content' => '[{"data": {"badge": "Expertise", "title": "Nossos Serviços", "description": null, "background_image": null, "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"steps": [], "title": null}, "type": "timeline"}, {"data": {"items": [], "title": null}, "type": "testimonials"}, {"data": {"badge": "ATENDIMENTO", "title": "Fale conosco", "description": "Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.", "call_enabled": true, "email_enabled": true, "whatsapp_enabled": true}, "type": "contact_banner"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => '{"title": null, "keywords": null, "og_image": null, "description": null}',
                'created_at' => '2026-09-28 18:56:37',
                'updated_at' => '2026-09-29 03:42:06',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'title' => 'Nossas Obras',
                'slug' => 'nossas-obras',
                'content' => '[{"data": {"badge": "PORTFÓLIO", "title": "Nossas Obras", "description": "Conheça alguns dos projetos realizados pela ConcretoPro em toda a região.", "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"limit": 10, "title": "Portfólio de Obras"}, "type": "showcase"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => NULL,
                'created_at' => '2026-09-28 18:56:37',
                'updated_at' => '2026-09-28 18:56:37',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 4,
                'title' => 'Sobre Nós',
                'slug' => 'sobre-nos',
                'content' => '[{"data": {"badge": "QUEM SOMOS", "title": "Compromisso com a Solidez da sua Obra", "description": "Desde a fundação até o acabamento, a Codhous é sua parceira em concreto usinado de alta performance.", "background_image": "about/betoneira-premium.png", "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"content": "<h2>Nossa História</h2><p>Com mais de 15 anos de atuação no mercado de construção civil, a Codhous nasceu com o propósito de desmistificar o fornecimento de concreto usinado. Entendemos que cada obra é única e que o cronograma é sagrado. Por isso, investimos em tecnologia de traço e logística de precisão para garantir que o seu projeto nunca pare.</p><p>Hoje, somos referência no Rio de Janeiro, atendendo desde pequenas reformas residenciais até grandes complexos industriais, sempre com o mesmo padrão de rigor técnico e atendimento personalizado.</p>"}, "type": "rich_text"}, {"data": {"items": [{"icon": "target", "title": "Nossa Missão", "description": "Fornecer soluções em concreto com agilidade, precisão e sustentabilidade, contribuindo para a segurança e durabilidade das construções de nossos clientes."}, {"icon": "eye", "title": "Nossa Visão", "description": "Ser a concreteira mais confiável e inovadora do estado, reconhecida pela excelência técnica e pelo compromisso com o sucesso de cada obra."}, {"icon": "shield-check", "title": "Nossos Valores", "description": "Ética nas negociações, rigor técnico no traço, pontualidade britânica e respeito absoluto ao meio ambiente e às normas de segurança."}], "title": "Nossos Pilares", "subtitle": null, "description": null}, "type": "differentials"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => NULL,
                'created_at' => '2026-09-28 18:56:37',
                'updated_at' => '2026-09-28 18:56:37',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'title' => 'Contato',
                'slug' => 'contato',
                'content' => '[{"data": {"badge": "CONTATO", "title": "Fale Conosco", "description": "Tire suas dúvidas ou solicite uma visita técnica.", "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"items": [], "title": null}, "type": "faq"}, {"data": {"title": "Envie sua Mensagem", "description": "Preencha o formulário abaixo e retornaremos o mais breve possível."}, "type": "contact_form"}, {"data": {"title": null, "iframe_code": null}, "type": "map"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => NULL,
                'created_at' => '2026-09-28 18:56:37',
                'updated_at' => '2026-09-28 18:56:37',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 9,
                'title' => 'Homepage',
                'slug' => '/',
                'content' => '[{"data": {"content_section_id": "1"}, "type": "module_reference"}, {"data": {"content_section_id": "2"}, "type": "module_reference"}, {"data": {"title": "Calculadora de Volume"}, "type": "calculator"}, {"data": {"content_section_id": "4"}, "type": "module_reference"}, {"data": {"badge": "APROVEITE ESSA MEGA OPORTUNIDADE", "title": "Parcelamento em até 12x sem juros", "subtitle": "ou com desconto no pagamento à vista em dinheiro ou com o pix.", "button_url": null, "button_label": "Fazer orçamento grátis", "background_image": null, "payment_methods_image": null}, "type": "payment_offer"}, {"data": {"title": "Orçamento Grátis", "description": "Solicite seu orçamento online, grátis e sem compromisso. Resposta em até 48h."}, "type": "budget_form"}, {"data": {"badge": "Nossos Projetos", "limit": "4", "title": "Nossas Obras", "description": null}, "type": "showcase"}, {"data": {"content_section_id": "6"}, "type": "module_reference"}, {"data": {"content_section_id": "7"}, "type": "module_reference"}, {"data": {"content_section_id": "9"}, "type": "module_reference"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => -1,
                'meta' => '{"title": null, "keywords": null, "og_image": null, "description": null}',
                'created_at' => '2026-10-02 02:00:05',
                'updated_at' => '2026-10-02 04:53:17',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}