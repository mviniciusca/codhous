<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Projeto editável do Estúdio de Posts. O documento gráfico fica em
 * `document` (ver App\Services\PosterStudio\PosterDocument); exportações
 * são geradas no navegador e nunca substituem o documento.
 */
class PosterDesign extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'document' => 'array',
            'revision' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (PosterDesign $design) {
            if ($design->thumbnail) {
                Storage::disk('public')->delete($design->thumbnail);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? Storage::disk('public')->url($this->thumbnail).'?v='.$this->revision : null;
    }

    /**
     * Raiz da família de variações (o próprio projeto quando não é variação).
     */
    public function rootId(): int
    {
        return $this->parent_id ?? $this->id;
    }
}
