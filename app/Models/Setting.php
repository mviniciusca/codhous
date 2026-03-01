<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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
            static::$current_instance = static::find(1);
        }
        return static::$current_instance;
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
}
