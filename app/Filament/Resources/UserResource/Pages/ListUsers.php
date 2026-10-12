<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\AccessArea;
use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Team\InvitationService;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

/**
 * Equipe e acessos: usuários, convites, perfis e registro de acessos em abas.
 */
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.team.index';

    public const SECTIONS = [
        'usuarios' => ['label' => 'Usuários', 'icon' => 'heroicon-m-users'],
        'convites' => ['label' => 'Convites', 'icon' => 'heroicon-m-envelope'],
        'perfis' => ['label' => 'Perfis de acesso', 'icon' => 'heroicon-m-shield-check'],
        'logs' => ['label' => 'Registro de acessos', 'icon' => 'heroicon-m-clock'],
    ];

    #[Url(as: 'aba')]
    public string $section = 'usuarios';

    public function mount(): void
    {
        parent::mount();

        if (! array_key_exists($this->section, self::SECTIONS)) {
            $this->section = 'usuarios';
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Equipe e acessos';
    }

    public function getSubheading(): ?string
    {
        return 'Quem entra no painel, com qual perfil, e o histórico de acessos.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('createWithPassword')
                    ->label('Criar acesso com senha')
                    ->icon('heroicon-o-key')
                    ->url(UserResource::getUrl('create')),
                Actions\Action::make('activityLog')
                    ->label('Registro de atividades completo')
                    ->icon('heroicon-o-clock')
                    ->url(fn () => ActivityResource::getUrl('index'))
                    ->visible(fn () => auth()->user()->canAccessArea(AccessArea::Logs)),
            ])
                ->label('Mais')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button(),
            $this->inviteAction(),
        ];
    }

    public function inviteAction(): Actions\Action
    {
        return Actions\Action::make('invite')
            ->label('Convidar usuário')
            ->icon('heroicon-o-user-plus')
            ->authorize(fn () => auth()->user()->can('create', User::class))
            ->modalHeading('Convidar para a equipe')
            ->modalDescription('A pessoa recebe um link por e-mail, válido por '.UserInvitation::EXPIRES_IN_DAYS.' dias, para criar a própria senha.')
            ->modalIcon('heroicon-o-envelope')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Enviar convite')
            ->form([
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email')
                    ->validationMessages(['unique' => 'Já existe um usuário com este e-mail.'])
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        if (UserInvitation::query()->pending()->where('email', mb_strtolower(trim((string) $value)))->exists()) {
                            $fail('Já existe um convite pendente para este e-mail. Reenvie pela aba Convites.');
                        }
                    }),
                TextInput::make('name')
                    ->label('Nome')
                    ->maxLength(255)
                    ->placeholder('Opcional: a pessoa pode ajustar'),
                Select::make('role_id')
                    ->label('Perfil de acesso')
                    ->options(fn () => UserResource::roleOptions())
                    ->default(fn () => Role::query()->where('name', 'Comercial')->value('id'))
                    ->required()
                    ->native(false)
                    ->helperText('Pode ser trocado depois.'),
            ])
            ->action(function (array $data) {
                $invitation = app(InvitationService::class)->invite($data['email'], $data['name'] ?? null, Role::findOrFail($data['role_id']), auth()->user());

                Notification::make()
                    ->title('Convite enviado')
                    ->body("{$invitation->email} recebeu o link por e-mail.")
                    ->success()
                    ->send();

                $this->section = 'convites';
                $this->dispatch('invitation-sent');
            });
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getTeamStats(): array
    {
        $active = User::query()->where('is_active', true)->count();
        $inactive = User::query()->where('is_active', false)->count();
        $newThisMonth = User::query()->where('created_at', '>=', now(config('app.display_timezone'))->startOfMonth()->utc())->count();
        $pending = UserInvitation::query()->pending()->count();
        $expired = UserInvitation::query()->open()->where('expires_at', '<=', now())->count();
        $roles = Role::query()->where('guard_name', 'web')->orderByDesc('is_system')->orderBy('name')->pluck('name');

        return [
            'active' => [
                'value' => (string) $active,
                'note' => collect([
                    $newThisMonth > 0 ? "{$newThisMonth} ".($newThisMonth === 1 ? 'novo' : 'novos').' este mês' : null,
                    $inactive > 0 ? "{$inactive} ".($inactive === 1 ? 'inativo' : 'inativos') : null,
                ])->filter()->implode(' · ') ?: 'Ninguém desativado',
                'tone' => 'success',
            ],
            'pending' => [
                'value' => (string) $pending,
                'note' => $expired > 0
                    ? "{$expired} ".($expired === 1 ? 'convite vencido' : 'convites vencidos')
                    : ($pending > 0 ? 'Aguardando aceite' : 'Nenhum aguardando'),
                'tone' => $expired > 0 ? 'danger' : ($pending > 0 ? 'warning' : 'gray'),
            ],
            'roles' => [
                'value' => (string) $roles->count(),
                'note' => $roles->implode(', '),
                'tone' => 'primary',
            ],
        ];
    }
}
