<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BackgroundImage extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Register Spatie Media Library conversions.
     * Creates a lightweight thumbnail for the gallery picker
     * and a WebP optimized version of the original.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->format('webp')
            ->quality(75)
            ->nonQueued(); // synchronous so the grid loads immediately

        $this->addMediaConversion('preview')
            ->width(1080)
            ->height(1080)
            ->format('webp')
            ->quality(85);
    }

    /**
     * Register media collections.
     * 'image' holds the single background image per record.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    /**
     * Helper to get the thumbnail URL.
     */
    public function getThumbnailUrl(): string
    {
        return $this->getFirstMediaUrl('image', 'thumb');
    }

    /**
     * Helper to get the full preview URL used in the generator.
     */
    public function getPreviewUrl(): string
    {
        return $this->getFirstMediaUrl('image', 'preview')
            ?: $this->getFirstMediaUrl('image');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
