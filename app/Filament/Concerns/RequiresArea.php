<?php

namespace App\Filament\Concerns;

use App\Enums\AccessArea;

/**
 * Página (fora de resource) restrita a uma área de acesso. O Filament só confere
 * canAccess() no mount; o hook de hydrate repete a checagem em cada requisição.
 */
trait RequiresArea
{
    abstract public static function getAccessArea(): AccessArea;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->canAccessArea(static::getAccessArea());
    }

    public function hydrateRequiresArea(): void
    {
        abort_unless(static::canAccess(), 403);
    }
}
