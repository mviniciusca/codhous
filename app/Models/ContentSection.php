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


    public const TYPE_PARTNERS = 'partners';
    public const TYPE_SERVICES = 'services';
    public const TYPE_FAQ = 'faq';
    public const TYPE_TESTIMONIALS = 'testimonials';
    public const TYPE_COVERAGE = 'coverage';
    public const TYPE_DIFFERENTIALS = 'differentials';
    public const TYPE_TIMELINE = 'timeline';
    public const TYPE_CTA_CONTACT = 'cta_contact';
    public const TYPE_CONTACT_BANNER = 'contact_banner';


    public static function typeLabels(): array
    {
        return [
            self::TYPE_PARTNERS => 'Parceiros',
            self::TYPE_SERVICES => 'Serviços',
            self::TYPE_FAQ => 'FAQ',
            self::TYPE_TESTIMONIALS => 'Depoimentos',
            self::TYPE_COVERAGE => 'Onde Atuamos',
            self::TYPE_DIFFERENTIALS => 'Diferenciais',
            self::TYPE_TIMELINE => 'Como Funciona (Timeline)',
            self::TYPE_CTA_CONTACT => 'Contato',
            self::TYPE_CONTACT_BANNER => 'Banner de Atendimento',
        ];
    }

    public static function slugForType(string $type): string
    {
        return match ($type) {
            self::TYPE_PARTNERS => 'partners',
            self::TYPE_SERVICES => 'services',
            self::TYPE_FAQ => 'faq',
            self::TYPE_TESTIMONIALS => 'testimonials',
            self::TYPE_COVERAGE => 'coverage',
            self::TYPE_DIFFERENTIALS => 'differentials',
            self::TYPE_TIMELINE => 'timeline',
            self::TYPE_CTA_CONTACT => 'cta_contact',
            self::TYPE_CONTACT_BANNER => 'contact-banner',
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


}
