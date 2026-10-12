<?php

namespace App\Policies;

use App\Enums\AccessArea;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Gestão da equipe. Só quem tem a área "Equipe" (perfil Administrador) administra
 * usuários, e ninguém altera o próprio acesso por aqui: os próprios dados são
 * editados em "Meu perfil", que pede a senha atual.
 */
class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->canAccessArea(AccessArea::Equipe);
    }

    public function view(User $actor, User $user): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return $this->viewAny($actor);
    }

    public function update(User $actor, User $user): Response
    {
        if (! $this->viewAny($actor)) {
            return Response::deny();
        }

        return $actor->is($user)
            ? Response::deny('Edite seus próprios dados em “Meu perfil”.')
            : Response::allow();
    }

    public function changeRole(User $actor, User $user): Response
    {
        if (! $this->viewAny($actor)) {
            return Response::deny();
        }

        return $actor->is($user)
            ? Response::deny('Você não pode alterar o seu próprio perfil de acesso.')
            : Response::allow();
    }

    public function toggleActive(User $actor, User $user): Response
    {
        if (! $this->viewAny($actor)) {
            return Response::deny();
        }

        if ($actor->is($user)) {
            return Response::deny('Você não pode desativar a sua própria conta.');
        }

        if ($user->is_active && $this->isLastActiveAdmin($user)) {
            return Response::deny('É preciso manter pelo menos um administrador ativo.');
        }

        return Response::allow();
    }

    public function delete(User $actor, User $user): Response
    {
        if (! $this->viewAny($actor)) {
            return Response::deny();
        }

        if ($actor->is($user)) {
            return Response::deny('Você não pode remover a sua própria conta.');
        }

        if ($this->isLastActiveAdmin($user)) {
            return Response::deny('É preciso manter pelo menos um administrador ativo.');
        }

        // Sem dono, o orçamento passaria a constar como pedido feito pelo site
        if ($user->budgets()->withoutGlobalScopes()->exists()) {
            return Response::deny('Este usuário tem orçamentos. Desative a conta para manter o histórico.');
        }

        return Response::allow();
    }

    public function deleteAny(User $actor): bool
    {
        return $this->viewAny($actor);
    }

    public function restore(User $actor, User $user): bool
    {
        return false;
    }

    public function forceDelete(User $actor, User $user): bool
    {
        return false;
    }

    protected function isLastActiveAdmin(User $user): bool
    {
        return $user->is_active
            && $user->isAdmin()
            && User::query()->activeAdmins()->whereKeyNot($user->getKey())->doesntExist();
    }
}
