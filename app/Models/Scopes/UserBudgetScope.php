<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserBudgetScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Filters budgets for vendedores to show only their own.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Super admin and admin see all budgets
        if ($user->hasRole(['super_admin', 'admin', 'financeiro', 'atendimento'])) {
            return;
        }

        // Vendedor sees only their own budgets
        if ($user->hasRole('vendedor')) {
            $builder->where('user_id', $user->id);
        }
    }
}
