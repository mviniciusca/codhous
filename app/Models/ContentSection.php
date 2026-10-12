<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSection extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'name',
        'type',
        'content',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];


    public const TYPE_HERO = 'hero';
    public const HERO_LAYOUT_DEFAULT = 'default';
    public const TYPE_PARTNERS = 'partners';
    public const TYPE_COMMERCIAL_PARTNERS = 'commercial_partners';
    public const TYPE_SERVICES = 'services';
    public const TYPE_FAQ = 'faq';
    public const TYPE_TESTIMONIALS = 'testimonials';
    public const TYPE_COVERAGE = 'coverage';
    public const TYPE_DIFFERENTIALS = 'differentials';
    public const TYPE_TIMELINE = 'timeline';
    public const TYPE_CTA_CONTACT = 'cta_contact';
    public const TYPE_CONTACT_BANNER = 'contact_banner';
    public const TYPE_BUDGET_FORM = 'budget_form';
    public const TYPE_CALCULATOR = 'calculator';
    public const TYPE_PAYMENT_OFFER = 'payment_offer';
    public const TYPE_SIMPLE_BANNER = 'simple_banner';
    public const TYPE_TEAM = 'team';
    public const TYPE_SHOWCASE = 'showcase';
    public const TYPE_EQUIPMENT_SHOWCASE = 'equipment_showcase';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_HERO => 'Hero Section',
            self::TYPE_PARTNERS => 'Clientes (Logos)',
            self::TYPE_COMMERCIAL_PARTNERS => 'Parceiros Comerciais',
            self::TYPE_SERVICES => 'Serviços',
            self::TYPE_FAQ => 'FAQ',
            self::TYPE_TESTIMONIALS => 'Depoimentos',
            self::TYPE_COVERAGE => 'Onde Atuamos',
            self::TYPE_DIFFERENTIALS => 'Diferenciais',
            self::TYPE_TIMELINE => 'Como Funciona (Timeline)',
            self::TYPE_CTA_CONTACT => 'Contato',
            self::TYPE_CONTACT_BANNER => 'Banner de Atendimento',
            self::TYPE_BUDGET_FORM => 'Formulário de Orçamento',
            self::TYPE_CALCULATOR => 'Calculadora de Volume',
            self::TYPE_PAYMENT_OFFER => 'Oferta de Pagamento',
            self::TYPE_SIMPLE_BANNER => 'Banner Simples (Imagem e Link)',
            self::TYPE_TEAM => 'Nosso Time',
            self::TYPE_SHOWCASE => 'Galeria de Obras (Showcase)',
            self::TYPE_EQUIPMENT_SHOWCASE => 'Showcase de Equipamentos',
        ];
    }

    /**
     * Categorias da galeria de tipos (etapa "Tipo da Seção").
     *
     * @return array<string, array{label: string, icon: string}>
     */
    public static function typeCategories(): array
    {
        return [
            'highlight' => ['label' => 'Destaques', 'icon' => 'heroicon-o-star'],
            'company' => ['label' => 'Empresa', 'icon' => 'heroicon-o-building-office-2'],
            'portfolio' => ['label' => 'Portfólio', 'icon' => 'heroicon-o-photo'],
            'social_proof' => ['label' => 'Prova social', 'icon' => 'heroicon-o-hand-thumb-up'],
            'service' => ['label' => 'Atendimento', 'icon' => 'heroicon-o-chat-bubble-left-ellipsis'],
            'conversion' => ['label' => 'Conversão', 'icon' => 'heroicon-o-cursor-arrow-rays'],
        ];
    }

    /**
     * Metadados de cada tipo para a biblioteca: ícone, descrição curta, categorias
     * e termos de busca extras. A miniatura (skeleton) fica em resources/views/filament/forms/components/section-type-picker/previews/{type}.
     *
     * @return array<string, array{label: string, description: string, icon: string, categories: array<int, string>, keywords: string}>
     */
    public static function typeCatalog(): array
    {
        $meta = [
            self::TYPE_HERO => ['Destaque principal no topo da página, com mídia de fundo e chamada.', 'heroicon-o-star', ['highlight'], 'banner topo capa cep whatsapp slideshow video'],
            self::TYPE_PARTNERS => ['Faixa com os logotipos dos seus clientes.', 'heroicon-o-building-office', ['social_proof'], 'logos marcas empresas'],
            self::TYPE_COMMERCIAL_PARTNERS => ['Parceiros e fornecedores com logo e descrição.', 'heroicon-o-briefcase', ['social_proof'], 'fornecedores parceria'],
            self::TYPE_SERVICES => ['Cards com os serviços oferecidos pela empresa.', 'heroicon-o-wrench-screwdriver', ['company'], 'servicos produtos cards'],
            self::TYPE_FAQ => ['Perguntas e respostas em acordeão.', 'heroicon-o-question-mark-circle', ['service'], 'perguntas duvidas respostas acordeao'],
            self::TYPE_TESTIMONIALS => ['Carrossel com depoimentos e avaliações de clientes.', 'heroicon-o-chat-bubble-left-right', ['social_proof'], 'avaliacoes opinioes reviews carrossel'],
            self::TYPE_COVERAGE => ['Mapa e cidades onde a empresa atende.', 'heroicon-o-map', ['service'], 'mapa cidades regiao area atendimento'],
            self::TYPE_DIFFERENTIALS => ['Vantagens competitivas com ícones.', 'heroicon-o-sparkles', ['company'], 'vantagens beneficios porque escolher'],
            self::TYPE_TIMELINE => ['Passo a passo do processo, em etapas numeradas.', 'heroicon-o-clock', ['company'], 'etapas processo como funciona passos'],
            self::TYPE_CTA_CONTACT => ['Informações de contato com formulário de mensagem.', 'heroicon-o-phone', ['service', 'conversion'], 'formulario email telefone endereco mensagem'],
            self::TYPE_CONTACT_BANNER => ['Faixa de chamada rápida para atendimento.', 'heroicon-o-megaphone', ['service'], 'cta chamada whatsapp faixa'],
            self::TYPE_BUDGET_FORM => ['Formulário em etapas para solicitar orçamento.', 'heroicon-o-currency-dollar', ['conversion'], 'orcamento cotacao pedido wizard'],
            self::TYPE_CALCULATOR => ['Calcula o volume de concreto da obra.', 'heroicon-o-calculator', ['conversion'], 'volume metros cubicos m3 concreto'],
            self::TYPE_PAYMENT_OFFER => ['Condições de pagamento e meios aceitos.', 'heroicon-o-credit-card', ['highlight', 'conversion'], 'parcelamento cartao pix oferta promocao'],
            self::TYPE_SIMPLE_BANNER => ['Imagem em largura total com link.', 'heroicon-o-photo', ['highlight'], 'imagem link anuncio'],
            self::TYPE_TEAM => ['Membros da equipe com foto e cargo.', 'heroicon-o-users', ['company'], 'equipe pessoas colaboradores time'],
            self::TYPE_SHOWCASE => ['Galeria com fotos de obras realizadas.', 'heroicon-o-camera', ['portfolio'], 'galeria obras fotos projetos'],
            self::TYPE_EQUIPMENT_SHOWCASE => ['Vitrine de equipamentos e máquinas.', 'heroicon-o-truck', ['portfolio'], 'equipamentos maquinas locacao frota'],
        ];

        $catalog = [];

        foreach (static::typeLabels() as $type => $label) {
            [$description, $icon, $categories, $keywords] = $meta[$type] ?? ['', 'heroicon-o-squares-2x2', [], ''];

            $catalog[$type] = compact('label', 'description', 'icon', 'categories', 'keywords');
        }

        return $catalog;
    }

    public static function slugForType(string $type): string
    {
        return match ($type) {
            self::TYPE_HERO => 'hero',
            self::TYPE_PARTNERS => 'partners',
            self::TYPE_COMMERCIAL_PARTNERS => 'commercial-partners',
            self::TYPE_SERVICES => 'services',
            self::TYPE_FAQ => 'faq',
            self::TYPE_TESTIMONIALS => 'testimonials',
            self::TYPE_COVERAGE => 'coverage',
            self::TYPE_DIFFERENTIALS => 'differentials',
            self::TYPE_TIMELINE => 'timeline',
            self::TYPE_CTA_CONTACT => 'cta_contact',
            self::TYPE_CONTACT_BANNER => 'contact-banner',
            self::TYPE_BUDGET_FORM => 'budget-form',
            self::TYPE_CALCULATOR => 'calculator',
            self::TYPE_PAYMENT_OFFER => 'payment-offer',
            self::TYPE_SIMPLE_BANNER => 'simple-banner',
            self::TYPE_TEAM => 'team',
            self::TYPE_SHOWCASE => 'showcase',
            self::TYPE_EQUIPMENT_SHOWCASE => 'equipment-showcase',
            default => $type,
        };
    }

    /**
     * Ordem da listagem: sort_order (vazios por último) e, no empate, a mais antiga primeiro.
     */
    public function scopeOrdered($query)
    {
        return $query->orderByRaw('sort_order is null')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Onde cada seção é usada: blocos `module_reference` do conteúdo publicado das páginas.
     * A posição é a ordem do bloco na página (1 = primeiro). A homepage vem primeiro.
     *
     * @return array<int, array<int, array{page_id: int, title: string, is_home: bool, is_visible: bool, position: int}>>
     */
    public static function usageMap(): array
    {
        $map = [];

        Page::query()->get(['id', 'title', 'slug', 'content', 'is_visible'])->each(function (Page $page) use (&$map) {
            foreach (array_values($page->content ?? []) as $index => $block) {
                $sectionId = (int) data_get($block, 'data.content_section_id');

                if (data_get($block, 'type') !== 'module_reference' || $sectionId === 0) {
                    continue;
                }

                $map[$sectionId][] = [
                    'page_id' => $page->id,
                    'title' => (string) $page->title,
                    'is_home' => $page->isHomepage(),
                    'is_visible' => (bool) $page->is_visible,
                    'position' => $index + 1,
                ];
            }
        });

        foreach ($map as &$usages) {
            usort($usages, fn (array $a, array $b): int => [$b['is_home'], $a['position']] <=> [$a['is_home'], $b['position']]);
        }

        return $map;
    }

    public function scopeForSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::forSlug($slug)->active()->first();
    }

    public static function isHidden(string $slug): bool
    {
        $section = static::where('slug', $slug)->first();
        return $section ? !$section->is_active : false;
    }

    public static function getActiveHero(): ?self
    {
        return static::where('type', self::TYPE_HERO)->active()->first();
    }
}
