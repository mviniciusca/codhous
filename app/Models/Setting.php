<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    protected static $current_instance = null;

    /**
     * Get the single settings instance.
     */
    public static function current(): ?self
    {
        if (static::$current_instance === null) {
            static::$current_instance = static::first();
        }

        return static::$current_instance;
    }

    protected static bool $previewing = false;

    /**
     * Renderiza o restante da requisição com configurações não salvas (prévia do Design e SEO).
     * Todas as views leem por Setting::get(), então basta trocar a instância memorizada.
     */
    public static function previewWith(array $settings): void
    {
        static::$current_instance = new static(['settings' => $settings]);
        static::$previewing = true;
    }

    /** Volta a ler do banco (fim da prévia; processos persistentes como Octane) */
    public static function endPreview(): void
    {
        static::$current_instance = null;
        static::$previewing = false;
    }

    public static function isPreviewing(): bool
    {
        return static::$previewing;
    }

    /**
     * Mescla configurações salvas com novas. Listas (ex: links do menu) são
     * substituídas inteiras; mesclar por índice ressuscitaria itens removidos.
     */
    public static function merge(array $current, array $changes): array
    {
        foreach ($changes as $key => $value) {
            $current[$key] = is_array($value) && ! array_is_list($value) && is_array($current[$key] ?? null) && ! array_is_list($current[$key])
                ? static::merge($current[$key], $value)
                : $value;
        }

        return $current;
    }

    /**
     * Get a specific setting value using dot notation.
     * Example: Setting::get('website.name')
     */
    public static function get(string $key, $default = null)
    {
        $settings = static::current();

        return data_get($settings?->settings, $key, $default);
    }

    public function getWebsiteAttribute(): array
    {
        return $this->settings['website'] ?? [];
    }

    public function getCompanyAttribute(): array
    {
        return $this->settings['company'] ?? [];
    }

    public function getSecurityAttribute(): array
    {
        return $this->settings['security'] ?? [];
    }

    /**
     * Get company settings as an object for easier access in views
     */
    public function getCompanySettingAttribute(): ?object
    {
        $company = $this->settings['company'] ?? null;

        return $company ? json_decode(json_encode($company)) : null;
    }

    /**
     * Get layout settings as an object for easier access in views
     */
    public function getLayoutSettingAttribute(): ?object
    {
        $layout = $this->settings['layout'] ?? null;

        return $layout ? json_decode(json_encode($layout)) : null;
    }
}
