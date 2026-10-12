<?php

namespace App\Livewire\Team\Concerns;

use App\Enums\AccessArea;

/**
 * Componentes da tela de equipe: só quem tem a área Equipe, conferido no mount
 * e de novo em cada requisição (o componente pode ficar aberto após perder o acesso).
 */
trait AuthorizesTeamAccess
{
    public function mountAuthorizesTeamAccess(): void
    {
        $this->authorizeTeamAccess();
    }

    public function hydrateAuthorizesTeamAccess(): void
    {
        $this->authorizeTeamAccess();
    }

    protected function authorizeTeamAccess(): void
    {
        abort_unless(auth()->user()?->canAccessArea(AccessArea::Equipe), 403);
    }
}
