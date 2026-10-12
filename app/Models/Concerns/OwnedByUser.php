<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro pessoal: só o dono enxerga e altera. Toda leitura passa por ownedBy(),
 * então o id de outro usuário simplesmente não é encontrado.
 */
trait OwnedByUser
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, ?User $user): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $user?->getKey() ?? 0);
    }
}
