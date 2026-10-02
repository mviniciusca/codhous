<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContentSectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('content_sections')->delete();
        
        \DB::table('content_sections')->insert(array (
            0 => 
            array (
                'id' => 1,
                'slug' => 'hero',
                'name' => 'Hero Section',
                'type' => 'hero',
                'content' => '{"image": "hero/01M3X7E7SN7J8PE53G9S1YVBMQ.jpg", "stats": [{"label": "Obras atendidas", "value": "500+"}, {"label": "Pontualidade", "value": "98%"}, {"label": "Anos de experiência", "value": "15+"}], "video": "hero/01M3X8AJQHM0H186N61GMSVNH4.mp4", "header": {"title": "Concreto Usinado em 48h na sua obra.", "subtitle": "Rio e Grande Rio", "description": null}, "layout": "default", "show_stats": true, "text_color": "light", "show_slideshow": false, "image_alignment": "top", "background_color": "bg-white", "background_image": null, "primary_button_url": "#", "primary_button_icon": "mail", "primary_button_text": "Suporte de Vendas", "show_action_buttons": true, "secondary_button_url": "#", "secondary_button_text": "Não sabe o quanto precisa? Calculadora grátis.", "primary_button_icon_select": "outro", "secondary_button_icon_select": "calculator"}',
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-10-02 16:29:27',
            ),
            1 => 
            array (
                'id' => 2,
                'slug' => 'partners',
                'name' => 'Parceiros',
                'type' => 'partners',
                'content' => '{"items": [{"icon": "building-2", "logo": "partners/01M3YE7ZTQ7WE5X85F4BFV8VHR.png", "name": "MRV Engenharia", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "hard-hat", "logo": "partners/01M3YE7ZTWB0QYFXNKQ4BHYKMG.webp", "name": "Construtora Tenda", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "landmark", "logo": "partners/01M3YE7ZTX6ERWPHCMM5DKCWHC.png", "name": "Cyrela Brazil", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "factory", "logo": "partners/01M3YE7ZTX6ERWPHCMM5DKCWHD.png", "name": "Gafisa S.A.", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "warehouse", "logo": "partners/01M3YEA2GGZPQ0X9NVGFAH7WP5.png", "name": "Even Construtora", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "hammer", "logo": "partners/01M3YE7ZTY9GRVWB8T7ZBWZS2N.png", "name": "Direcional Eng.", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "construction", "logo": "partners/01M3YE7ZTZ0VAJZ63SGKE4MDHN.png", "name": "Cury Construtora", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}, {"icon": "ruler", "logo": "partners/01M3YE7ZTZ0VAJZ63SGKE4MDHP.png", "name": "Plano & Plano", "quote": null, "stars": null, "title": null, "answer": null, "bullets": [], "cta_url": null, "question": null, "subtitle": null, "cta_label": null, "author_name": null, "author_role": null, "description": null}], "header": {"title": "Nossa empresa faz parte da vida dos brasileiros", "subtitle": "Confiam na Codhous", "description": "Empresas que estão continuamente com o nosso trabalho, confiam na história e na nossa excelência"}, "layout": "grid", "text_color": "dark", "background_color": "bg-primary text-primary-foreground"}',
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-10-02 15:12:18',
            ),
            2 => 
            array (
                'id' => 3,
                'slug' => 'services',
                'name' => 'Serviços',
                'type' => 'services',
                'content' => '{"items": [{"icon": "droplets", "title": "Concreto Usinado", "bullets": ["FCK 20 a 50 MPa", "Traço personalizado", "Nota fiscal e certificado"], "cta_url": "#orcamento", "subtitle": "por m³", "cta_label": "Solicitar Orçamento", "description": "Concreto de alta qualidade com controle rigoroso de traço e resistência."}, {"icon": "gauge", "title": "Bombeamento de Concreto", "bullets": ["Bomba estacionária", "Bomba lança", "Até 40m de altura"], "cta_url": "#orcamento", "subtitle": "por serviço", "cta_label": "Solicitar Orçamento", "description": "Serviço de bombeamento para lajes, fundações e estruturas em altura."}, {"icon": "wrench", "title": "Locação de Máquinas", "bullets": ["Retroescavadeira", "Minicarregadeira", "Compactador de solo"], "cta_url": "#orcamento", "subtitle": "diária / hora", "cta_label": "Solicitar Orçamento", "description": "Equipamentos de ponta para sua obra."}], "header": {"title": "Tudo que sua obra precisa em um só lugar", "subtitle": "Nossos Serviços", "description": "Da fundação ao acabamento, oferecemos soluções completas com qualidade garantida."}}',
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-09-28 18:56:36',
            ),
            3 => 
            array (
                'id' => 4,
                'slug' => 'timeline',
            'name' => 'Como Funciona (Timeline)',
                'type' => 'timeline',
                'content' => '{"steps": [{"icon": "message-square-text", "title": "Solicite o Orçamento", "step_label": "Etapa 1", "description": "Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra."}, {"icon": "clipboard-check", "title": "Aprovação do Traço", "step_label": "Etapa 2", "description": "Nossos engenheiros definem o traço ideal para a necessidade e o tipo da sua estrutura."}, {"icon": "factory", "title": "Produção na Usina", "step_label": "Etapa 3", "description": "O concreto é produzido com controle de qualidade rigoroso e rastreabilidade total."}, {"icon": "truck", "title": "Entrega na Obra", "step_label": "Etapa 4", "description": "Frota própria com rastreamento entrega o concreto no horário combinado."}], "header": {"title": "Como funciona", "subtitle": "Processo Simplificado", "description": "Do orçamento à entrega, tudo pensado para facilitar sua obra."}}',
                'is_active' => 1,
                'sort_order' => 3,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-09-28 18:56:36',
            ),
            4 => 
            array (
                'id' => 5,
                'slug' => 'faq',
                'name' => 'FAQ',
                'type' => 'faq',
                'content' => '{"items": [{"answer": "Em horário comercial, respondemos em até 2 horas.", "question": "Qual o prazo para receber o orçamento?"}, {"answer": "Sim. Trabalhamos com agendamento e nossa logística garante alta taxa de pontualidade.", "question": "Vocês entregam no dia que eu precisar?"}, {"answer": "Geralmente, empresas de concretagem estabelecem um volume mínimo por viagem de 3 m³ para otimizar o transporte, conforme guias de boas práticas baseados nas normas.", "question": "Qual o volume mínimo de concreto?"}, {"answer": "Sim. Todos os carregamentos são acompanhados de nota fiscal e laudo de resistência.", "question": "O concreto vem com nota fiscal e certificado?"}, {"answer": "Sim. Oferecemos serviço de bombeamento. O orçamento pode incluir concreto + bombeamento.", "question": "Posso solicitar bombeamento junto com o concreto?"}, {"answer": "Aceitamos Pix, débito, cartão de crédito e também podemos combinar a melhor condição para atender as necessidades do seu projeto.", "question": "Quais as formas de pagamento aceitas?"}], "header": {"title": "Perguntas frequentes", "subtitle": "Dúvidas Frequentes", "description": "Respostas rápidas sobre concreto usinado, entrega e orçamento."}}',
                'is_active' => 1,
                'sort_order' => 4,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-09-28 18:56:36',
            ),
            5 => 
            array (
                'id' => 6,
                'slug' => 'testimonials',
                'name' => 'Depoimentos',
                'type' => 'testimonials',
                'content' => '{"items": [{"quote": "\\"Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível.\\"", "stars": 5, "author_name": "Carlos Mendes", "author_role": "Engenheiro"}, {"quote": "\\"Pontualidade e qualidade do traço fazem a diferença.\\"", "stars": 5, "author_name": "Ana Paula Costa", "author_role": "Mestre de obras"}, {"quote": "\\"Orçamento claro, entrega no horário e suporte pós-venda.\\"", "stars": 5, "author_name": "Roberto Lima", "author_role": "Arquiteto"}], "header": {"title": "O que dizem nossos clientes", "subtitle": "Depoimentos", "description": "Empresas e obras que confiam na nossa entrega e no nosso suporte."}}',
                'is_active' => 1,
                'sort_order' => 5,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-09-28 18:56:36',
            ),
            6 => 
            array (
                'id' => 7,
                'slug' => 'coverage',
                'name' => 'Onde Atuamos',
                'type' => 'coverage',
                'content' => '{"cities": ["Rio de Janeiro", "Niterói", "São Gonçalo", "Duque de Caxias", "Nova Iguaçu", "Belford Roxo", "São João de Meriti"], "header": {"title": "Onde atendemos", "subtitle": "Cobertura", "description": "Atuamos na região com frota própria e logística integrada."}, "sidebar": [{"icon": null, "title": "Raio de entrega", "description": "Consulte disponibilidade e prazo para sua cidade no orçamento."}, {"icon": null, "title": "Frota própria", "description": "Rastreamento e pontualidade em todas as entregas."}], "background_media": "coverage/01M3XH61XRHKWRVMCSWWFCEXNB.jpg"}',
                'is_active' => 1,
                'sort_order' => 6,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-10-02 05:25:23',
            ),
            7 => 
            array (
                'id' => 8,
                'slug' => 'differentials',
                'name' => 'Diferenciais',
                'type' => 'differentials',
                'content' => '{"items": [{"icon": "clock", "title": "Zero Atrasos", "description": "Nosso sistema de logística GPS garante que seu concreto chegue no horário programado."}, {"icon": "microscope", "title": "Laboratório", "description": "Testamos rigorosamente cada lote em nosso laboratório próprio seguindo as normas NBR."}, {"icon": "smartphone", "title": "App de Gestão", "description": "Acompanhe seu pedido e gerencie seus orçamentos pelo smartphone."}, {"icon": "award", "title": "Selo Verde", "description": "Comprometidos com a sustentabilidade através do reuso de água e gestão de resíduos."}], "header": {"title": "Excelência técnica em cada carregamento", "subtitle": "Por que nos escolher", "description": "Estamos redefinindo o padrão de atendimento no mercado de concreto usinado."}}',
                'is_active' => 1,
                'sort_order' => 7,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-09-28 18:56:36',
            ),
            8 => 
            array (
                'id' => 9,
                'slug' => 'cta_contact',
                'name' => 'Contato',
                'type' => 'cta_contact',
                'content' => '{"header": {"title": "Fale Conosco", "subtitle": "SAC", "description": "Escolha como entrar em contato com a nossa equipe"}, "email_to": "mviniciusca@oi.com.br", "budget_btn_url": null, "address_enabled": true, "budget_btn_title": "Vem", "email_btn_enabled": true, "phone_btn_enabled": true, "budget_btn_enabled": true, "budget_btn_subtitle": "vem de zap", "whatsapp_btn_enabled": true}',
                'is_active' => 1,
                'sort_order' => 8,
                'created_at' => '2026-09-28 18:56:36',
                'updated_at' => '2026-10-02 04:35:13',
            ),
            9 => 
            array (
                'id' => 10,
                'slug' => 'budget-form',
                'name' => 'Formulário de Orçamento',
                'type' => 'budget_form',
                'content' => '[]',
                'is_active' => 1,
                'sort_order' => 10,
                'created_at' => '2026-10-02 15:35:22',
                'updated_at' => '2026-10-02 15:35:22',
            ),
            10 => 
            array (
                'id' => 11,
                'slug' => 'calculator',
                'name' => 'Calculadora de Volume',
                'type' => 'calculator',
                'content' => '{"header": {"title": "Calculadora de Volume", "subtitle": "Bage tem que ser mais escuro", "description": null}, "text_color": "dark", "background_color": "bg-foreground text-background", "background_image": "sections/backgrounds/01M3YNKGKTBDAR3WYZ1B9DMH9R.png", "background_image_fit": "contain", "background_image_opacity": null, "background_image_pull_up": "10", "background_image_position": "right"}',
                'is_active' => 1,
                'sort_order' => 11,
                'created_at' => '2026-10-02 15:35:22',
                'updated_at' => '2026-10-02 16:23:04',
            ),
            11 => 
            array (
                'id' => 12,
                'slug' => 'payment-offer',
                'name' => 'Oferta de Pagamento',
                'type' => 'payment_offer',
                'content' => '{"header": {"title": "Compre online e parcele em até 12x", "subtitle": "Black Friday", "description": "Faça sua compra com nossa equipe no chat de vendas online"}, "button_url": null, "text_color": "light", "button_label": "Televendas", "background_color": "bg-white", "background_image": null, "payment_methods_image": "sections/payment/01M3YR133RSPY63JE4C1NBTAM3.png"}',
                'is_active' => 1,
                'sort_order' => 7,
                'created_at' => '2026-10-02 16:42:02',
                'updated_at' => '2026-10-02 16:54:30',
            ),
            12 => 
            array (
                'id' => 13,
                'slug' => 'simple-banner',
            'name' => 'Banner Simples (Imagem e Link)',
                'type' => 'simple_banner',
                'content' => '{"image": "sections/banners/01M3YSVXSM30A4N9EDV3TFRDNS.png", "header": {"title": null, "subtitle": null, "description": null}, "link_url": null, "text_color": "light", "open_in_new_tab": true, "background_color": "bg-transparent", "background_image": null}',
                'is_active' => 1,
                'sort_order' => 11,
                'created_at' => '2026-10-02 16:55:03',
                'updated_at' => '2026-10-02 17:17:59',
            ),
        ));
        
        
    }
}