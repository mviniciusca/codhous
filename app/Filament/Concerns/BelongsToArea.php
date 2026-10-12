<?php

namespace App\Filament\Concerns;

use App\Enums\AccessArea;
use Illuminate\Database\Eloquent\Model;

/**
 * Resource restrito a uma área de acesso. Resource::can() é o ponto único que o
 * Filament consulta para navegação, busca global, ações e o acesso às páginas do
 * resource (no mount e em cada requisição Livewire). Policies do model, quando
 * existirem, continuam valendo depois da área.
 */
trait BelongsToArea
{
    abstract public static function getAccessArea(): AccessArea;

    public static function can(string $action, ?Model $record = null): bool
    {
        if (! auth()->user()?->canAccessArea(static::getAccessArea())) {
            return false;
        }

        return parent::can($action, $record);
    }
}
