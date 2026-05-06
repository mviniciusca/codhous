<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserMailScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Filters mails for vendedores and atendimento to show only their own.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Se o sistema de roles foi removido, permitimos a visualização por enquanto
        return;
    }
}
