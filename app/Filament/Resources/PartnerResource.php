<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use App\Models\Setting;
use App\Services\AddressFinder;
use App\Services\PostcodeFinder;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationGroup = 'Empresa';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count != 0 ? $count : null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Parceiros';
    }

    public static function getModelLabel(): string
    {
        return 'Parceiro';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Parceiros';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Group::make()
                    ->columns(5)
                    ->schema([
                        Section::make('Informações do Parceiro')
                            ->icon('heroicon-o-briefcase')
                            ->description('Dados cadastrais e de contato da empresa parceira.')
                            ->columnSpan(4)
                            ->columns(3)
                            ->schema([
                                Forms\Components\Hidden::make('setting_id')
                                    ->default(Setting::first()->id)
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Nome da Empresa')
                                    ->helperText('Razão social ou nome fantasia do parceiro.')
                                    ->lazy()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->maxLength(255),
                                Forms\Components\Hidden::make('slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label('Slug'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label('E-mail')
                                    ->helperText('Endereço de e-mail para comunicações oficiais.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.phone')
                                    ->required()
                                    ->tel()
                                    ->mask('(99) 99999-9999')
                                    ->placeholder('(00) 00000-0000')
                                    ->helperText('Telefone de contato com DDD.')
                                    ->label('Telefone'),
                                Forms\Components\TextInput::make('postcode')
                                    ->label('CEP')
                                    ->helperText('Digite o CEP para buscar o endereço.')
                                    ->required()
                                    ->minLength(9)
                                    ->mask('99999-999')
                                    ->placeholder('00000-000')
                                    ->maxLength(9)
                                    ->suffixAction(
                                        fn ($state, Set $set, $livewire) => Action::make('search-cep')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->tooltip('Buscar endereço pelo CEP')
                                            ->action(function () use ($state, $livewire, $set) {
                                                try {
                                                    $livewire->validateOnly('data.postcode');
                                                    $fieldMap = [
                                                        'logradouro' => 'content.address',
                                                        'bairro'     => 'content.neighborhood',
                                                        'localidade' => 'content.city',
                                                        'uf'         => 'content.state',
                                                    ];
                                                    $finder = new AddressFinder($state, $set, $fieldMap, 'data.postcode');
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
                                Forms\Components\TextInput::make('content.address')
                                    ->label('Logradouro')
                                    ->helperText('Rua, Avenida, etc. (Preenchido via CEP).')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.number')
                                    ->label('Número')
                                    ->helperText('Número do imóvel ou S/N.')
                                    ->columnSpan(1)
                                    ->maxLength(140),
                                Forms\Components\TextInput::make('content.neighborhood')
                                    ->label('Bairro')
                                    ->helperText('Bairro da localização.')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.city')
                                    ->label('Cidade')
                                    ->helperText('Cidade do parceiro.')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.state')
                                    ->label('UF')
                                    ->helperText('Estado/Província.')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255),
                            ]),
                        Section::make('Status e Visibilidade')
                            ->description('Controle o status do parceiro.')
                            ->columnSpan(1)
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Ativo')
                                    ->helperText('Define se o parceiro será exibido no site.')
                                    ->inline(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->alignCenter()
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content.phone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->default(true)
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit'   => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
