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

        // Super admin and admin see all mails
        if ($user->hasRole(['super_admin', 'admin'])) {
            return;
        }

        // Vendedor and atendimento see only their own mails
        if ($user->hasRole(['vendedor', 'atendimento'])) {
            $builder->where('user_id', $user->id);
        }

        // Financeiro has no access to mails
        if ($user->hasRole('financeiro')) {
            $builder->whereRaw('1 = 0'); // Never show any mails
        }
    }
}
