<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Gestão da Equipe';
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
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Usuário')
                    ->icon('heroicon-o-user')
                    ->description('Dados básicos e credenciais de acesso ao sistema.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Foto de Perfil')
                            ->avatar()
                            ->image()
                            ->directory('avatars')
                            ->columnSpanFull()
                            ->alignCenter(),
                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->helperText('Nome que aparecerá no sistema e orçamentos.')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail de Acesso')
                            ->helperText('Utilizado para login e notificações.')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Senha')
                            ->helperText('Mínimo 8 caracteres. Deixe em branco para manter a atual.')
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->prefixIcon('heroicon-o-key')
                            ->helperText('Confirme a nova senha digitada ao lado.')
                            ->label('Confirmar Senha')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Foto')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('budgets_count')
                    ->label('Orçamentos')
                    ->counts('budgets')
                    ->badge()
                    ->color('success')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filtros removidos por simplicidade
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum usuário encontrado')
            ->emptyStateDescription('Crie o seu primeiro membro de equipe agora mesmo.')
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
