<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
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
                'id' => 3,
                'title' => 'Nossas Obras',
                'slug' => 'nossas-obras',
                'content' => '[{"data": {"badge": "Portfolio", "title": "Nossas Obras", "description": "Conheça alguns dos projetos realizados pela ConcretoPro em toda a região.", "background_image": "headers/01M3YVKH8F5J1N2GY8X51V803E.jpg", "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"badge": "Showcase", "limit": 10, "title": "Portfólio de Obras", "description": "Conheça o nosso trabalho de perto. Nosso diário da obra está pronto para a sua visita."}, "type": "showcase"}, {"data": {"content_section_id": "6"}, "type": "module_reference"}, {"data": {"content_section_id": "2"}, "type": "module_reference"}, {"data": {"content_section_id": "9"}, "type": "module_reference"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => '{"title": null, "keywords": null, "og_image": null, "description": null}',
                'created_at' => '2026-09-28 18:56:37',
                'updated_at' => '2026-10-02 17:48:09',
                'deleted_at' => NULL,
            ),
            1 => 
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
            2 => 
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
            3 => 
            array (
                'id' => 9,
                'title' => 'Homepage',
                'slug' => '/',
                'content' => '[{"data": {"content_section_id": "1"}, "type": "module_reference"}, {"data": {"content_section_id": "2"}, "type": "module_reference"}, {"data": {"content_section_id": "4"}, "type": "module_reference"}, {"data": {"content_section_id": "11"}, "type": "module_reference"}, {"data": {"content_section_id": "13"}, "type": "module_reference"}, {"data": {"content_section_id": "6"}, "type": "module_reference"}, {"data": {"content_section_id": "7"}, "type": "module_reference"}, {"data": {"content_section_id": "9"}, "type": "module_reference"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => -1,
                'meta' => '{"title": null, "keywords": null, "og_image": null, "description": null}',
                'created_at' => '2026-10-02 02:00:05',
                'updated_at' => '2026-10-02 17:26:13',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 10,
                'title' => 'Serviços',
                'slug' => 'servicos',
                'content' => '[{"data": {"badge": "Serviços", "title": "Nossos Serviços", "description": "Descubra como podemos auxiliar na sua construção. Conte com quem está no mercado há mais de 10 anos.", "background_image": "headers/01M3YV01KP81MQVXN8MDCZ2KJ9.jpg", "show_breadcrumbs": true}, "type": "page_header"}, {"data": {"content_section_id": "3"}, "type": "module_reference"}, {"data": {"content_section_id": "13"}, "type": "module_reference"}, {"data": {"badge": "Showcase", "limit": 4, "title": "Nossas Obras", "description": "Conheça nosso trabalho mais de perto. Leia no nosso blog da obra.\\n"}, "type": "showcase"}, {"data": {"content_section_id": "12"}, "type": "module_reference"}, {"data": {"content_section_id": "9"}, "type": "module_reference"}]',
                'is_active_in_menu' => 1,
                'is_visible' => 1,
                'sort_order' => 0,
                'meta' => '{"title": null, "keywords": null, "og_image": null, "description": null}',
                'created_at' => '2026-10-02 17:36:06',
                'updated_at' => '2026-10-02 17:44:55',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}