<?php

namespace App\Models;

use App\Services\PosterStudio\BrandKit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Imagem da biblioteca de mídia do Estúdio de Posts.
 */
class PosterAsset extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(fn (PosterAsset $asset) => Storage::disk($asset->disk)->delete($asset->path));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function url(): string
    {
        return BrandKit::relativeUrl(Storage::disk($this->disk)->url($this->path));
    }

    public function toLibraryArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url(),
            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,
            'mime' => $this->mime_type,
            'category' => $this->category,
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
