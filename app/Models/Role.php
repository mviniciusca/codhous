<?php

namespace App\Models;

use App\Enums\AccessArea;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Perfil de acesso. O perfil de sistema (Administrador) não pode ser editado nem removido.
 */
class Role extends SpatieRole
{
    public const ADMIN = 'Administrador';

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public static function admin(): self
    {
        return static::findOrCreate(self::ADMIN, 'web');
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    /**
     * @return Collection<int, AccessArea>
     */
    public function areas(): Collection
    {
        $granted = $this->permissions->pluck('name');

        return collect(AccessArea::cases())->filter(fn (AccessArea $area) => $granted->contains($area->permission()))->values();
    }

    /**
     * @param  array<int, string>  $areas  valores de AccessArea
     */
    public function syncAreas(array $areas): void
    {
        $allowed = array_keys(AccessArea::assignableOptions());

        $this->syncPermissions(
            collect($areas)->intersect($allowed)->map(fn (string $area) => AccessArea::from($area)->permission())->values()->all()
        );
    }
}
