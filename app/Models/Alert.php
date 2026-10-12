<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    protected static function booted(): void
    {
        // Posição sempre compatível com o tipo (o campo some do formulário para modal)
        static::saving(function (Alert $alert): void {
            $alert->position = $alert->resolvedPosition();
        });
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_EXPIRED = 'expired';

    /**
     * Tipos de alerta: rótulo, descrição, ícone (painel) e posições aceitas.
     * A primeira posição é a padrão. O site (x-site-alerts) segue este mapa.
     *
     * @return array<string, array{label: string, description: string, icon: string, positions: list<string>}>
     */
    public static function typeCatalog(): array
    {
        return [
            self::TYPE_MODAL => [
                'label' => 'Modal / Popup',
                'description' => 'Janela no centro da tela, com fundo escurecido.',
                'icon' => 'heroicon-o-square-3-stack-3d',
                'positions' => [self::POSITION_CENTER],
            ],
            self::TYPE_TOAST => [
                'label' => 'Toast (canto)',
                'description' => 'Cartão discreto em um dos cantos da tela.',
                'icon' => 'heroicon-o-bell-alert',
                'positions' => [self::POSITION_BOTTOM_RIGHT, self::POSITION_BOTTOM_LEFT, self::POSITION_TOP_RIGHT, self::POSITION_TOP_LEFT],
            ],
            self::TYPE_BANNER => [
                'label' => 'Banner fixo',
                'description' => 'Faixa de largura total no topo ou no rodapé.',
                'icon' => 'heroicon-o-view-columns',
                'positions' => [self::POSITION_TOP, self::POSITION_BOTTOM],
            ],
        ];
    }

    /**
     * Estilos visuais: rótulo, ícone e cor (tons do painel).
     *
     * @return array<string, array{label: string, icon: string, color: string}>
     */
    public static function styleCatalog(): array
    {
        return [
            self::STYLE_INFO => ['label' => 'Informativo', 'icon' => 'heroicon-o-information-circle', 'color' => 'info'],
            self::STYLE_PROMO => ['label' => 'Promoção', 'icon' => 'heroicon-o-gift', 'color' => 'purple'],
            self::STYLE_ANNOUNCEMENT => ['label' => 'Aviso', 'icon' => 'heroicon-o-megaphone', 'color' => 'warning'],
            self::STYLE_CONSENT => ['label' => 'Consentimento', 'icon' => 'heroicon-o-shield-check', 'color' => 'gray'],
            self::STYLE_WARNING => ['label' => 'Atenção', 'icon' => 'heroicon-o-exclamation-triangle', 'color' => 'danger'],
            self::STYLE_SUCCESS => ['label' => 'Sucesso', 'icon' => 'heroicon-o-check-circle', 'color' => 'success'],
        ];
    }

    /** @return array<string, array{label: string, icon: string}> */
    public static function positionCatalog(): array
    {
        return [
            self::POSITION_TOP => ['label' => 'Topo', 'icon' => 'heroicon-o-arrow-up'],
            self::POSITION_BOTTOM => ['label' => 'Rodapé', 'icon' => 'heroicon-o-arrow-down'],
            self::POSITION_TOP_LEFT => ['label' => 'Topo esq.', 'icon' => 'heroicon-o-arrow-up-left'],
            self::POSITION_TOP_RIGHT => ['label' => 'Topo dir.', 'icon' => 'heroicon-o-arrow-up-right'],
            self::POSITION_BOTTOM_LEFT => ['label' => 'Rodapé esq.', 'icon' => 'heroicon-o-arrow-down-left'],
            self::POSITION_BOTTOM_RIGHT => ['label' => 'Rodapé dir.', 'icon' => 'heroicon-o-arrow-down-right'],
            self::POSITION_CENTER => ['label' => 'Centro', 'icon' => 'heroicon-o-arrows-pointing-in'],
        ];
    }

    /** @return array<string, array{label: string, icon: string, color: string}> */
    public static function statusCatalog(): array
    {
        return [
            self::STATUS_ACTIVE => ['label' => 'Ativo', 'icon' => 'heroicon-m-check-circle', 'color' => 'success'],
            self::STATUS_SCHEDULED => ['label' => 'Agendado', 'icon' => 'heroicon-m-calendar', 'color' => 'warning'],
            self::STATUS_EXPIRED => ['label' => 'Encerrado', 'icon' => 'heroicon-m-clock', 'color' => 'gray'],
            self::STATUS_DRAFT => ['label' => 'Rascunho', 'icon' => 'heroicon-m-pencil-square', 'color' => 'gray'],
        ];
    }

    public static function typeLabels(): array
    {
        return array_map(fn (array $type): string => $type['label'], static::typeCatalog());
    }

    public static function styleLabels(): array
    {
        return array_map(fn (array $style): string => $style['label'], static::styleCatalog());
    }

    public static function positionLabels(): array
    {
        return array_map(fn (array $position): string => $position['label'], static::positionCatalog());
    }

    /** @return list<string> */
    public static function positionsFor(?string $type): array
    {
        return static::typeCatalog()[$type]['positions'] ?? array_keys(static::positionCatalog());
    }

    public static function defaultPositionFor(?string $type): string
    {
        return static::positionsFor($type)[0] ?? self::POSITION_TOP;
    }

    /**
     * Posição efetiva no site: uma posição incompatível com o tipo (dados antigos)
     * cai na padrão do tipo, como o site já fazia.
     */
    public function resolvedPosition(): string
    {
        $positions = static::positionsFor($this->type);

        if (in_array($this->position, $positions, true)) {
            return $this->position;
        }

        // Toasts antigos sem canto definido apareciam no topo à direita
        return $this->type === self::TYPE_TOAST ? self::POSITION_TOP_RIGHT : $positions[0];
    }

    /**
     * Link do botão só com destinos seguros: http(s), mailto, tel, caminho
     * interno (/pagina) ou âncora (#secao). Qualquer outro esquema (javascript:,
     * data:...) é descartado.
     */
    public static function sanitizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || preg_match('/[\x00-\x1F\x7F\s]/', $url)) {
            return null;
        }

        if (str_starts_with($url, '#') || (str_starts_with($url, '/') && ! str_starts_with($url, '//') && ! str_starts_with($url, '/\\'))) {
            return $url;
        }

        if (preg_match('/^(https?):\/\/[^\s\/?#]+/i', $url)) {
            return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
        }

        if (preg_match('/^(mailto|tel):[^\s]+$/i', $url)) {
            return $url;
        }

        return null;
    }

    public function safeCtaUrl(): ?string
    {
        return static::sanitizeUrl($this->cta_url);
    }

    public function hasCta(): bool
    {
        return filled($this->cta_label) && $this->safeCtaUrl() !== null;
    }

    /** Link externo abre em nova aba; interno, na mesma */
    public function ctaOpensInNewTab(): bool
    {
        $url = (string) $this->safeCtaUrl();

        return preg_match('/^https?:\/\//i', $url) === 1
            && parse_url($url, PHP_URL_HOST) !== request()->getHost();
    }

    public function status(?Carbon $now = null): string
    {
        $now ??= now();

        if (! $this->is_active) {
            return self::STATUS_DRAFT;
        }

        if ($this->start_at && $this->start_at->greaterThan($now)) {
            return self::STATUS_SCHEDULED;
        }

        if ($this->end_at && $this->end_at->lessThan($now)) {
            return self::STATUS_EXPIRED;
        }

        return self::STATUS_ACTIVE;
    }

    /** Dias que um alerta fechado fica oculto quando "Lembrar fechamento" está ligado */
    public const DEFAULT_COOKIE_DAYS = 3;

    /** Nome interno do aviso de cookies padrão (seeder e migração de instalações antigas) */
    public const COOKIE_CONSENT_NAME = 'Consentimento de cookies';

    /**
     * Aviso de cookies (LGPD) que antes era o componente fixo site-cookie-consent.
     * Usa o mesmo cookie de antes, então quem já aceitou continua sem ver o aviso.
     *
     * @return array<string, mixed>
     */
    public static function cookieConsentDefaults(): array
    {
        return [
            'name' => self::COOKIE_CONSENT_NAME,
            'type' => self::TYPE_BANNER,
            'style' => self::STYLE_CONSENT,
            'position' => self::POSITION_BOTTOM,
            'title' => 'Valorizamos sua privacidade',
            'message' => 'Utilizamos cookies e tecnologias semelhantes para melhorar a sua experiência em nosso site, analisar o tráfego e personalizar o conteúdo. Ao continuar navegando, você concorda com a nossa política de privacidade.',
            'is_active' => true,
            'is_dismissible' => true,
            'use_cookie' => true,
            'cookie_key' => 'codhous_cookie_consent',
            'cookie_duration_days' => 7,
            'cta_label' => null,
            'cta_url' => null,
            'start_at' => null,
            'end_at' => null,
            'sort_order' => 0,
        ];
    }

    public function getCookieKey(): string
    {
        return $this->cookie_key ?: 'alert_dismissed_'.$this->id;
    }

    public function getCookieDurationDays(): int
    {
        return (int) ($this->cookie_duration_days ?? self::DEFAULT_COOKIE_DAYS);
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

    public static function getActiveForFrontend(): Collection
    {
        return static::active()->orderBy('sort_order')->orderBy('id')->get();
    }
}
