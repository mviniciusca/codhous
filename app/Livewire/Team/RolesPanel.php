<?php

namespace App\Livewire\Team;

use App\Enums\AccessArea;
use App\Livewire\Team\Concerns\AuthorizesTeamAccess;
use App\Models\Role;
use App\Services\Team\InvitationService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Perfis de acesso: o que cada um libera, quantas pessoas usam e edição dos personalizados.
 */
class RolesPanel extends Component implements HasActions, HasForms
{
    use AuthorizesTeamAccess;
    use InteractsWithActions;
    use InteractsWithForms;

    /**
     * @return Collection<int, Role>
     */
    #[Computed]
    public function roles(): Collection
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();
    }

    public function createRoleAction(): Action
    {
        return Action::make('createRole')
            ->label('Novo perfil')
            ->icon('heroicon-m-plus')
            ->modalHeading('Novo perfil de acesso')
            ->modalWidth('lg')
            ->form($this->roleForm())
            ->action(function (array $data) {
                $role = Role::create(['name' => trim($data['name']), 'guard_name' => 'web', 'description' => $data['description'] ?? null]);
                $role->syncAreas($data['areas'] ?? []);
                $this->log($role, 'role_created', "Criou o perfil {$role->name}");

                Notification::make()->title('Perfil criado')->success()->send();
            });
    }

    public function editRoleAction(): Action
    {
        return Action::make('editRole')
            ->label('Editar')
            ->icon('heroicon-m-pencil-square')
            ->color('gray')
            ->size('sm')
            ->modalHeading(fn (array $arguments) => 'Editar '.$this->findEditable($arguments)->name)
            ->modalWidth('lg')
            ->fillForm(function (array $arguments) {
                $role = $this->findEditable($arguments);

                return [
                    'name' => $role->name,
                    'description' => $role->description,
                    'areas' => $role->areas()->map->value->all(),
                ];
            })
            ->form(fn (array $arguments) => $this->roleForm($this->findEditable($arguments)))
            ->action(function (array $data, array $arguments) {
                $role = $this->findEditable($arguments);
                $role->update(['name' => trim($data['name']), 'description' => $data['description'] ?? null]);
                $role->syncAreas($data['areas'] ?? []);
                $this->log($role, 'role_updated', "Alterou o perfil {$role->name}: ".($role->areas()->map->label()->implode(', ') ?: 'sem áreas'));

                Notification::make()->title('Perfil atualizado')->success()->send();
            });
    }

    public function deleteRoleAction(): Action
    {
        return Action::make('deleteRole')
            ->label('Remover')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments) => 'Remover o perfil '.$this->findEditable($arguments)->name.'?')
            ->modalDescription('Convites pendentes com este perfil também serão removidos.')
            ->modalSubmitActionLabel('Remover')
            ->action(function (array $arguments, Action $action) {
                $role = $this->findEditable($arguments);

                if ($role->users()->exists()) {
                    Notification::make()->title('Perfil em uso')->body('Mude o perfil das pessoas que usam este perfil antes de removê-lo.')->danger()->send();
                    $action->halt();
                }

                $this->log($role, 'role_deleted', "Removeu o perfil {$role->name}");
                $role->delete();

                Notification::make()->title('Perfil removido')->success()->send();
            });
    }

    protected function roleForm(?Role $role = null): array
    {
        return [
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(60)
                ->unique('roles', 'name', ignorable: $role, modifyRuleUsing: fn (Unique $rule) => $rule->where('guard_name', 'web'))
                ->notIn([Role::ADMIN])
                ->validationMessages(['not_in' => 'Este nome é reservado.']),
            TextInput::make('description')
                ->label('Descrição')
                ->maxLength(255)
                ->placeholder('Ex: Atendimento e vendas'),
            CheckboxList::make('areas')
                ->label('Áreas liberadas')
                ->options(AccessArea::assignableOptions())
                ->descriptions(collect(AccessArea::cases())->reject->isAdminOnly()->mapWithKeys(fn (AccessArea $area) => [$area->value => $area->description()])->all())
                ->helperText(AccessArea::Equipe->label().' é exclusivo do perfil Administrador.')
                ->required()
                ->validationMessages(['required' => 'Escolha pelo menos uma área.']),
        ];
    }

    /** Perfis de sistema (Administrador) não são editáveis nem removíveis */
    protected function findEditable(array $arguments): Role
    {
        $role = Role::query()->where('guard_name', 'web')->findOrFail($arguments['role'] ?? null);
        abort_if($role->is_system || $role->isAdmin(), 403);

        return $role;
    }

    protected function log(Role $role, string $event, string $description): void
    {
        activity(InvitationService::LOG)
            ->causedBy(auth()->user())
            ->event($event)
            ->withProperties(['role' => $role->name, 'areas' => $role->areas()->map->value->all()])
            ->log($description);
    }

    public function render()
    {
        return view('livewire.team.roles-panel', ['areas' => AccessArea::cases()]);
    }
}
