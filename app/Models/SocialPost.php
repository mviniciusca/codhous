<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'preset'          => \App\Enums\CardPreset::class,
        'overlay_opacity' => 'integer',
        'text_x'          => 'float',
        'text_y'          => 'float',
        'is_bold'         => 'boolean',
        'is_italic'       => 'boolean',
        'generated_at'    => 'datetime',
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
        return $this->status === 'done' && ! empty($this->output_path);
    }

    /**
     * Get full public URL for the generated image.
     */
    public function getOutputUrlAttribute(): ?string
    {
        if (! $this->output_path) {
            return null;
        }

        return asset('storage/' . $this->output_path);
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
