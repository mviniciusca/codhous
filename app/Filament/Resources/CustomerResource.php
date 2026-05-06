<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Http;
use App\Services\AddressFinderService;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationGroup = 'Empresa';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Clientes';
    }

    public static function getModelLabel(): string
    {
        return 'Cliente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Clientes';
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Cliente')
                    ->description('Gerencie os dados cadastrais e o endereço dos seus clientes.')
                    ->columns(3)
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->helperText('Informe o nome ou razão social.')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->helperText('Endereço de e-mail principal para contato.')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone/WhatsApp')
                            ->helperText('Número de contato com DDD.')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postcode')
                            ->label('CEP')
                            ->helperText('Digite o CEP para buscar o endereço.')
                            ->required()
                            ->mask('99999-999')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->placeholder('00000-000')
                            ->maxLength(255)
                            ->suffixAction(
                                fn ($state, Set $set, $livewire) => Action::make('search-cep')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->tooltip('Buscar endereço pelo CEP')
                                    ->action(function () use ($state, $livewire, $set) {
                                        try {
                                            $livewire->validateOnly('data.postcode');
                                            $fieldMap = [
                                                'logradouro' => 'address.street',
                                                'bairro'     => 'address.neighborhood',
                                                'localidade' => 'address.city',
                                                'uf'         => 'address.state',
                                            ];
                                            $finder = new AddressFinderService($state, $set, $fieldMap, 'data.postcode');
                                            $finder->find();
                                            \Filament\Notifications\Notification::make()
                                                ->title('CEP encontrado!')
                                                ->success()
                                                ->send();
                                        } catch (\Exception $e) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Erro ao buscar CEP')
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),
                        Forms\Components\TextInput::make('address.street')
                            ->label('Logradouro')
                            ->helperText('Rua, Avenida, etc.')
                            ->required(),
                        Forms\Components\TextInput::make('address.number')
                            ->label('Número')
                            ->helperText('Número ou S/N.'),
                        Forms\Components\TextInput::make('address.neighborhood')
                            ->label('Bairro')
                            ->helperText('Bairro ou Distrito.')
                            ->required(),
                        Forms\Components\TextInput::make('address.city')
                            ->label('Cidade')
                            ->helperText('Cidade do cliente.')
                            ->required(),
                        Forms\Components\TextInput::make('address.state')
                            ->label('UF')
                            ->helperText('Estado (Ex: SP, RJ).')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.city')
                    ->label('Cidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Lixeira'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir Permanente'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar Selecionados'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Excluir Permanente Selecionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
            'bin'    => Pages\CustomerBin::route('/bin'),
        ];
    }
}
