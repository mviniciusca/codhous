<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'name',
        'type',
        'style',
        'title',
        'message',
        'position',
        'is_active',
        'is_dismissible',
        'use_cookie',
        'cookie_key',
        'cookie_duration_days',
        'cta_label',
        'cta_url',
        'start_at',
        'end_at',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_dismissible' => 'boolean',
        'use_cookie' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public const TYPE_MODAL = 'modal';
    public const TYPE_TOAST = 'toast';
    public const TYPE_BANNER = 'banner';

    public const STYLE_INFO = 'info';
    public const STYLE_PROMO = 'promo';
    public const STYLE_ANNOUNCEMENT = 'announcement';
    public const STYLE_CONSENT = 'consent';
    public const STYLE_WARNING = 'warning';
    public const STYLE_SUCCESS = 'success';

    public const POSITION_TOP = 'top';
    public const POSITION_BOTTOM = 'bottom';
    public const POSITION_TOP_LEFT = 'top-left';
    public const POSITION_TOP_RIGHT = 'top-right';
    public const POSITION_BOTTOM_LEFT = 'bottom-left';
    public const POSITION_BOTTOM_RIGHT = 'bottom-right';
    public const POSITION_CENTER = 'center';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_MODAL => 'Modal / Popup',
            self::TYPE_TOAST => 'Toast (canto)',
            self::TYPE_BANNER => 'Banner fixo',
        ];
    }

    public static function styleLabels(): array
    {
        return [
            self::STYLE_INFO => 'Informativo',
            self::STYLE_PROMO => 'Promoção',
            self::STYLE_ANNOUNCEMENT => 'Aviso / Anúncio',
            self::STYLE_CONSENT => 'Consentimento (ex.: cookies)',
            self::STYLE_WARNING => 'Atenção',
            self::STYLE_SUCCESS => 'Sucesso',
        ];
    }

    public static function positionLabels(): array
    {
        return [
            self::POSITION_TOP => 'Topo (largura total)',
            self::POSITION_BOTTOM => 'Rodapé (largura total)',
            self::POSITION_TOP_LEFT => 'Canto superior esquerdo',
            self::POSITION_TOP_RIGHT => 'Canto superior direito',
            self::POSITION_BOTTOM_LEFT => 'Canto inferior esquerdo',
            self::POSITION_BOTTOM_RIGHT => 'Canto inferior direito',
            self::POSITION_CENTER => 'Centro (modal)',
        ];
    }

    public function getCookieKey(): string
    {
        return $this->cookie_key ?: 'alert_dismissed_' . $this->id;
    }

    public function getCookieDurationDays(): int
    {
        return (int) ($this->cookie_duration_days ?? 30);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            });
    }

    public static function getActiveForFrontend(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->orderBy('sort_order')->orderBy('id')->get();
    }
}
