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

    protected static ?string $navigationGroup = 'Customers';

    public static function getNavigationLabel(): string
    {
        return __('Customers');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function count(): ?string
    {
        $count = Customer::withoutTrashed()->count();

        return $count !== 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Customer'))
                    ->description(__('Manager your Customer List'))
                    ->columns(3)
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Customer Name'))
                            ->helperText(__('Set the customer name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email Address'))
                            ->helperText(__('Place here the email address from your customer'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->helperText(__('Customer Phone number'))
                            ->tel()
                            ->mask('(99)99999-9999')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postcode')
                            ->label(__('Postcode'))
                            ->helperText(__('Customer Postcode'))
                            ->required()
                            ->mask('99999-999')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->placeholder('----- ---')
                            ->maxLength(255)
                            ->suffixAction(
                                fn ($state, Set $set, $livewire) => Action::make('search-cep')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->tooltip(__('Search address by postcode'))
                                    ->action(function () use ($state, $livewire, $set) {
                                        try {
                                            // Validar o formato do CEP antes de fazer a busca
                                            $livewire->validateOnly('data.postcode');

                                            // Criar mapeamento de campos da API para campos do formulário
                                            $fieldMap = [
                                                'logradouro' => 'address.street',
                                                'bairro'     => 'address.neighborhood',
                                                'localidade' => 'address.city',
                                                'uf'         => 'address.state',
                                            ];

                                            // Instanciar e executar a busca de CEP
                                            $finder = new AddressFinderService($state, $set, $fieldMap, 'data.postcode');
                                            $finder->find();

                                            // Notificação de sucesso
                                            \Filament\Notifications\Notification::make()
                                                ->title('CEP encontrado!')
                                                ->success()
                                                ->send();
                                        } catch (\Exception $e) {
                                            // Em caso de erro, exibir notificação
                                            \Filament\Notifications\Notification::make()
                                                ->title('Erro')
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),
                        Forms\Components\TextInput::make('address.street')
                            ->label(__('Address Street'))
                            ->helperText(__('Address Street'))
                            ->required(),
                        Forms\Components\TextInput::make('address.number')
                            ->label(__('Address Number'))
                            ->helperText(__('Address Number')),
                        Forms\Components\TextInput::make('address.neighborhood')
                            ->label(__('Neighborhood'))
                            ->helperText(__('Neighborhood'))
                            ->required(),
                        Forms\Components\TextInput::make('address.city')
                            ->label(__('City'))
                            ->helperText(__('City'))
                            ->required(),
                        Forms\Components\TextInput::make('address.state')
                            ->label(__('State'))
                            ->helperText(__('State'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
