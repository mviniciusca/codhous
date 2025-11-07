<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
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

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Security';

    public static function getNavigationLabel(): string
    {
        return __('Team Management');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('User Information'))
                    ->icon('heroicon-o-user')
                    ->description(__('Basic user information and credentials'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->helperText(__('User full name'))
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email'))
                            ->helperText(__('User email for login'))
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->helperText(__('Minimum 8 characters'))
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8),

                        TextInput::make('password_confirmation')
                            ->prefixIcon('heroicon-o-key')
                            ->helperText(__('Confirm the password'))
                            ->label(__('Confirm Password'))
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false),
                    ]),

                Section::make(__('Role & Permissions'))
                    ->icon('heroicon-o-shield-check')
                    ->description(__('Assign role to user'))
                    ->schema([
                        Select::make('roles')
                            ->label(__('User Role'))
                            ->helperText(__('Select the role for this user. Super Admin: Full access | Admin: Manager | Vendedor: Sales | Financeiro: Financial | Atendimento: Customer Service'))
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                                'super_admin' => '🔴 Super Admin (Full Access)',
                                'admin'       => '🟡 Admin (Manager/Supervisor)',
                                'vendedor'    => '🟢 Vendedor (Sales Team)',
                                'financeiro'  => '🔵 Financeiro (Financial)',
                                'atendimento' => '🟣 Atendimento (Customer Service)',
                                default       => $record->name,
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextColumn::make('roles.name')
                    ->label(__('Role'))
                    ->badge()
                    ->colors([
                        'danger'  => 'super_admin',
                        'warning' => 'admin',
                        'success' => 'vendedor',
                        'info'    => 'financeiro',
                        'primary' => 'atendimento',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'super_admin' => 'Super Admin',
                            'admin'       => 'Admin',
                            'vendedor'    => 'Vendedor',
                            'financeiro'  => 'Financeiro',
                            'atendimento' => 'Atendimento',
                            default       => $state,
                        };
                    }),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('budgets_count')
                    ->label(__('Budgets'))
                    ->counts('budgets')
                    ->badge()
                    ->color('success')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Filter by Role'))
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple()
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'vendedor'    => 'Vendedor',
                        'financeiro'  => 'Financeiro',
                        'atendimento' => 'Atendimento',
                    ]),
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
            ->emptyStateHeading(__('No users yet'))
            ->emptyStateDescription(__('Create your first team member'))
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getRelations(): array
    {
        return [
            UserResource\RelationManagers\BudgetsRelationManager::class,
            UserResource\RelationManagers\ActivitiesRelationManager::class,
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
