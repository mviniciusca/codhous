<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
        'is_active_in_menu' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeInMenu($query)
    {
        return $query->where('is_active_in_menu', true);
    }

    protected static function booted()
    {
        static::saved(function ($page) {
            $setting = \App\Models\Setting::firstOrCreate(['id' => 1]);
            $settings = $setting->settings ?? [];
            $nav = $settings['website']['navigation'] ?? [];
            
            $url = $page->slug === '/' ? '/' : '/' . ltrim($page->slug, '/');
            $oldUrl = $page->getOriginal('slug');
            $oldUrl = $oldUrl === '/' ? '/' : '/' . ltrim((string)$oldUrl, '/');
            
            $index = -1;
            foreach ($nav as $i => $item) {
                if (($item['url'] ?? '') === $oldUrl || ($item['url'] ?? '') === $url) {
                    $index = $i;
                    break;
                }
            }
            
            if ($page->is_active_in_menu) {
                if ($index === -1) {
                    $nav[] = [
                        'label' => $page->title,
                        'url' => $url,
                    ];
                } else {
                    $nav[$index]['label'] = $page->title;
                    $nav[$index]['url'] = $url;
                }
            } else {
                if ($index !== -1) {
                    unset($nav[$index]);
                    $nav = array_values($nav);
                }
            }
            
            $settings['website']['navigation'] = $nav;
            $setting->update(['settings' => $settings]);
        });
        
        static::deleted(function ($page) {
            $setting = \App\Models\Setting::firstOrCreate(['id' => 1]);
            $settings = $setting->settings ?? [];
            $nav = $settings['website']['navigation'] ?? [];
            
            $url = $page->slug === '/' ? '/' : '/' . ltrim($page->slug, '/');
            
            $index = -1;
            foreach ($nav as $i => $item) {
                if (($item['url'] ?? '') === $url) {
                    $index = $i;
                    break;
                }
            }
            
            if ($index !== -1) {
                unset($nav[$index]);
                $nav = array_values($nav);
                $settings['website']['navigation'] = $nav;
                $setting->update(['settings' => $settings]);
            }
        });
    }
}
