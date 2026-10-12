<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Team\TeamService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Equipe e acessos. Só o perfil Administrador (área Equipe) chega aqui; as regras
 * por usuário (não mexer na própria conta, manter um administrador ativo, não
 * apagar quem tem orçamentos) ficam em App\Policies\UserPolicy.
 */
class UserResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Equipe;
    }

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationGroup = 'Configurações';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Equipe e acessos';
    }

    public static function getModelLabel(): string
    {
        return 'Usuário';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Usuários';
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = UserInvitation::query()->pending()->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Convites pendentes';
    }

    /**
     * @return array<int, string> id => nome
     */
    public static function roleOptions(): array
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados de acesso')
                    ->description('Nome, e-mail de login e perfil de acesso.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Foto')
                            ->avatar()
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->alignCenter(),
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail de acesso')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        // Perfil não passa por atribuição em massa: Create/EditUser aplicam via TeamService
                        Select::make('role_id')
                            ->label('Perfil de acesso')
                            ->options(fn () => self::roleOptions())
                            ->required()
                            ->native(false)
                            ->dehydrated(false)
                            ->disabled(fn (?User $record) => $record && Gate::denies('changeRole', $record))
                            ->helperText(fn (?User $record) => $record && Gate::denies('changeRole', $record)
                                ? Gate::inspect('changeRole', $record)->message()
                                : 'Define as áreas do painel que a pessoa acessa.'),
                    ]),

                Section::make('Senha')
                    ->description(fn (string $operation) => $operation === 'create'
                        ? 'Prefira convidar: a própria pessoa define a senha pelo link do convite.'
                        : 'Deixe em branco para manter a atual. Para não definir a senha por alguém, use “Enviar link de redefinição”.')
                    ->columns(2)
                    ->collapsed(fn (string $operation) => $operation === 'edit')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nova senha')
                            ->password()
                            ->revealable()
                            ->rule(PasswordRule::defaults()->min(8))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar senha')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn (string $operation, $get): bool => $operation === 'create' || filled($get('password')))
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles')->withLastLogin()->withCount('budgets'))
            ->columns([
                ViewColumn::make('avatar')
                    ->label('')
                    ->view('filament.team.avatar-column')
                    ->grow(false),
                TextColumn::make('name')
                    ->label('Usuário')
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (User $record) => $record->email)
                    ->suffix(fn (User $record) => $record->is(auth()->user()) ? '  · você' : null),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge()
                    ->color(fn (?string $state) => $state === Role::ADMIN ? 'primary' : 'gray')
                    ->placeholder('Sem perfil'),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Ativo' : 'Inativo')
                    ->color(fn (bool $state) => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-no-symbol')
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label('Último acesso')
                    ->since(config('app.display_timezone'))
                    ->dateTimeTooltip('d/m/Y H:i', config('app.display_timezone'))
                    ->placeholder('Nunca entrou')
                    ->sortable(),
                TextColumn::make('budgets_count')
                    ->label('Orçamentos')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Na equipe desde')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('role')
                    ->label('Perfil')
                    ->options(fn () => self::roleOptions())
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereHas('roles', fn (Builder $q) => $q->whereKey($data['value']))
                        : $query),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos')
                    ->placeholder('Todos'),
            ])
            ->recordUrl(fn (User $record) => Gate::allows('update', $record) ? self::getUrl('edit', ['record' => $record]) : null)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Ver e editar')
                        ->icon('heroicon-m-pencil-square'),

                    // A própria conta é editada em “Meu perfil” (pede a senha atual)
                    Action::make('ownProfile')
                        ->label('Editar meu perfil')
                        ->icon('heroicon-m-user-circle')
                        ->visible(fn (User $record) => $record->is(auth()->user()))
                        ->alpineClickHandler("\$dispatch('open-profile-modal')"),

                    Action::make('changeRole')
                        ->label('Alterar perfil')
                        ->icon('heroicon-m-shield-check')
                        ->visible(fn (User $record) => Gate::allows('changeRole', $record))
                        ->modalWidth('md')
                        ->modalHeading(fn (User $record) => "Perfil de {$record->name}")
                        ->fillForm(fn (User $record) => ['role_id' => $record->role()?->getKey()])
                        ->form([
                            Select::make('role_id')
                                ->label('Perfil de acesso')
                                ->options(fn () => self::roleOptions())
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (User $record, array $data) {
                            app(TeamService::class)->changeRole($record, Role::findOrFail($data['role_id']));

                            Notification::make()->title('Perfil atualizado')->success()->send();
                        }),

                    Action::make('sendPasswordReset')
                        ->label('Enviar link de redefinição')
                        ->icon('heroicon-m-key')
                        ->visible(fn (User $record) => Gate::allows('update', $record) && $record->is_active)
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-key')
                        ->modalHeading('Enviar link de redefinição de senha?')
                        ->modalDescription(fn (User $record) => "{$record->email} vai receber um link para criar uma nova senha.")
                        ->modalSubmitActionLabel('Enviar')
                        ->action(function (User $record) {
                            $status = app(TeamService::class)->sendPasswordReset($record);

                            $status === Password::RESET_LINK_SENT
                                ? Notification::make()->title('Link enviado para '.$record->email)->success()->send()
                                : Notification::make()->title('Não foi possível enviar agora')->body(__($status))->warning()->send();
                        }),

                    Action::make('toggleActive')
                        ->hidden(fn (User $record) => $record->is(auth()->user()))
                        ->label(fn (User $record) => $record->is_active ? 'Desativar acesso' : 'Reativar acesso')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-m-no-symbol' : 'heroicon-m-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                        ->disabled(fn (User $record) => Gate::denies('toggleActive', $record))
                        ->tooltip(fn (User $record) => Gate::inspect('toggleActive', $record)->message())
                        ->requiresConfirmation()
                        ->modalHeading(fn (User $record) => ($record->is_active ? 'Desativar' : 'Reativar')." o acesso de {$record->name}?")
                        ->modalDescription(fn (User $record) => $record->is_active
                            ? 'A pessoa sai do painel na hora e não consegue entrar de novo. O histórico e os orçamentos dela continuam.'
                            : 'A pessoa volta a entrar no painel com o perfil atual.')
                        ->modalSubmitActionLabel(fn (User $record) => $record->is_active ? 'Desativar' : 'Reativar')
                        ->action(function (User $record) {
                            app(TeamService::class)->setActive($record, ! $record->is_active);

                            Notification::make()->title($record->is_active ? 'Acesso reativado' : 'Acesso desativado')->success()->send();
                        }),

                    Action::make('delete')
                        ->hidden(fn (User $record) => $record->is(auth()->user()))
                        ->label('Remover')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->disabled(fn (User $record) => Gate::denies('delete', $record))
                        ->tooltip(fn (User $record) => Gate::inspect('delete', $record)->message())
                        ->requiresConfirmation()
                        ->modalHeading(fn (User $record) => "Remover {$record->name}?")
                        ->modalDescription('A conta é apagada de vez. Para só bloquear o acesso, use “Desativar acesso”.')
                        ->modalSubmitActionLabel('Remover')
                        ->action(function (User $record) {
                            app(TeamService::class)->delete($record);

                            Notification::make()->title('Usuário removido')->success()->send();
                        }),
                ])->tooltip('Ações'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Desativar acesso')
                    ->icon('heroicon-m-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Contas que você não pode desativar (a sua e a do último administrador) ficam como estão.')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        $done = $records
                            ->filter(fn (User $user) => $user->is_active && Gate::allows('toggleActive', $user))
                            ->each(fn (User $user) => app(TeamService::class)->setActive($user, false))
                            ->count();
                        $skipped = $records->count() - $done;

                        Notification::make()
                            ->title($done.' '.($done === 1 ? 'acesso desativado' : 'acessos desativados'))
                            ->body($skipped > 0 ? "{$skipped} ".($skipped === 1 ? 'conta ignorada' : 'contas ignoradas').'.' : null)
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Nenhum usuário encontrado')
            ->emptyStateDescription('Ajuste a busca ou os filtros, ou convide alguém para a equipe.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getRelations(): array
    {
        return [
            UserResource\RelationManagers\BudgetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
