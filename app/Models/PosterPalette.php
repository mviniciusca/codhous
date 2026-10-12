<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Paleta personalizada salva no Estúdio de Posts (papéis semânticos → hex).
 */
class PosterPalette extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['colors' => 'array'];
    }
}
