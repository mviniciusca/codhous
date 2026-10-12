<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use App\Services\Team\TeamService;
use Filament\Resources\Pages\CreateRecord;

/**
 * Criação direta, com senha definida pelo administrador. O caminho recomendado é o convite.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Criar acesso com senha';
    }

    public function getSubheading(): ?string
    {
        return 'Para que a própria pessoa escolha a senha, use “Convidar usuário” na tela da equipe.';
    }

    protected function afterCreate(): void
    {
        $role = Role::findOrFail($this->data['role_id']);
        $this->record->syncRoles([$role]);

        app(TeamService::class)->log($this->record, 'user_created', "Criou o acesso de {$this->record->name} como {$role->name}", ['role' => $role->name]);
    }
}
