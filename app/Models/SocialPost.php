<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SocialPost extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('output')
            ->singleFile();
    }

    protected $casts = [
        'preset'          => \App\Enums\CardPreset::class,
        'overlay_opacity' => 'integer',
        'text_x'          => 'float',
        'text_y'          => 'float',
        'is_bold'         => 'boolean',
        'is_italic'       => 'boolean',
        'generated_at'    => 'datetime',
        'layers'          => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function backgroundImage(): BelongsTo
    {
        return $this->belongsTo(BackgroundImage::class);
    }

    // ─── Helpers ──────────────────────────────────────────────

    /**
     * CSS rgba string for the overlay, used in the Blade preview.
     */
    public function getOverlayCssAttribute(): string
    {
        $hex = ltrim($this->overlay_color, '#');
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');
        $alpha = round($this->overlay_opacity / 100, 2);

        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    /**
     * Returns true if this post has a finished generated image.
     */
    public function isGenerated(): bool
    {
        return $this->status === 'done' && $this->hasMedia('output');
    }

    /**
     * Get full public URL for the generated image.
     */
    public function getOutputUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('output');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }
}
