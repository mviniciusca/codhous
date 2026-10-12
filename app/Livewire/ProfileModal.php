<?php

namespace App\Livewire;

use App\Services\Team\TeamService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/**
 * “Meu perfil” em modal, aberto pelo menu do usuário em qualquer tela do painel.
 * Só edita dados da própria conta; perfil de acesso e status não passam por aqui.
 * Trocar e-mail ou senha exige a senha atual.
 */
class ProfileModal extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function mount(): void
    {
        // /admin/profile redireciona para cá com ?perfil=1
        if (request()->boolean('perfil')) {
            $this->defaultAction = 'editProfile';
        }
    }

    public function editProfileAction(): Action
    {
        return Action::make('editProfile')
            ->label('Meu perfil')
            ->modalHeading('Meu perfil')
            ->modalDescription(fn () => 'Perfil de acesso: '.(auth()->user()->role()?->name ?? 'nenhum').'. Só um administrador pode alterá-lo.')
            ->modalIcon('heroicon-o-user-circle')
            ->modalWidth('xl')
            ->modalSubmitActionLabel('Salvar')
            ->closeModalByClickingAway(false)
            ->fillForm(fn () => [
                'avatar_url' => auth()->user()->avatar_url,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ])
            ->form([
                Section::make()
                    ->compact()
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->hiddenLabel()
                            ->avatar()
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('avatars')
                            ->visibility('public')
                            ->alignCenter(),
                        Grid::make(['default' => 1, 'sm' => 2])->schema([
                            TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(255)
                                ->autocomplete('name'),
                            TextInput::make('email')
                                ->label('E-mail de acesso')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->autocomplete('email')
                                ->unique('users', 'email', ignorable: fn () => auth()->user())
                                ->live(onBlur: true),
                        ]),
                    ]),
                Section::make('Trocar senha')
                    ->description('Deixe em branco para manter a senha atual.')
                    ->compact()
                    ->collapsible()
                    ->collapsed()
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        TextInput::make('password')
                            ->label('Nova senha')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->rule(Password::defaults()->min(8))
                            ->live(onBlur: true),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar nova senha')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->same('password')
                            ->required(fn (Get $get) => filled($get('password'))),
                    ]),
                TextInput::make('current_password')
                    ->label('Senha atual')
                    ->password()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->currentPassword()
                    ->visible(fn (Get $get) => $this->changesCredentials($get('email'), $get('password')))
                    ->required(fn (Get $get) => $this->changesCredentials($get('email'), $get('password')))
                    ->helperText('Necessária para trocar o e-mail ou a senha.'),
            ])
            ->action(function (array $data) {
                $user = auth()->user();
                $emailChanged = Str::lower(trim($data['email'])) !== Str::lower($user->email);
                $passwordChanged = filled($data['password'] ?? null);
                $previousEmail = $user->email;

                // Atribuição explícita: nada além destes campos muda pela tela de perfil
                $user->name = $data['name'];
                $user->avatar_url = $data['avatar_url'] ?? null;
                $user->email = Str::lower(trim($data['email']));
                if ($passwordChanged) {
                    $user->password = $data['password'];
                }
                $user->save();

                $team = app(TeamService::class);

                if ($passwordChanged) {
                    // Mantém esta sessão; as outras caem no AuthenticateSession (hash da senha mudou)
                    session()->put(['password_hash_'.Filament::getAuthGuard() => $user->getAuthPassword()]);
                    $team->log($user, 'profile_password_changed', 'Alterou a própria senha');
                }

                if ($emailChanged) {
                    $team->log($user, 'profile_email_changed', "Alterou o e-mail de acesso de {$previousEmail} para {$user->email}");
                }

                Notification::make()
                    ->title('Perfil atualizado')
                    ->body($passwordChanged ? 'Outras sessões abertas com a senha antiga foram encerradas.' : null)
                    ->success()
                    ->send();

                $this->dispatch('profile-updated');
            });
    }

    protected function changesCredentials(?string $email, ?string $password): bool
    {
        return filled($password) || Str::lower(trim((string) $email)) !== Str::lower((string) auth()->user()?->email);
    }

    public function render()
    {
        return view('livewire.profile-modal');
    }
}
