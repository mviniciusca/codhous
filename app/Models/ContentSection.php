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
