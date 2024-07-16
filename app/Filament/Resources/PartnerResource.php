<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Partner;
use App\Models\Setting;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\PartnerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PartnerResource\RelationManagers;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count != 0 ? $count : null;
    }

    public static function getNavigationLabel(): string
    {
        return __('Partners');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Group::make()
                    ->columns(5)
                    ->schema([
                        Section::make(__('Partner Information'))
                            ->columnSpan(4)
                            ->columns(2)
                            ->schema([
                                Forms\Components\Hidden::make('setting_id')
                                    ->default(Setting::first()->id)
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label(__('Company'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->label(__('Slug'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label(__('Email'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postcode')
                                    ->required()
                                    ->minLength(9)
                                    ->mask('99999-999')
                                    ->placeholder('22022-000')
                                    ->maxLength(9)
                                    ->helperText(__('Postcode'))
                                    ->required()
                                    ->label(__('Postcode'))
                                    ->suffixAction(
                                        fn($state, $set, $livewire) =>
                                        Action::make('search-action')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(function () use ($state, $livewire, $set) {
                                                $set('content.neighborhood', null);
                                                $set('content.address', null);
                                                $set('content.number', null);
                                                $set('content.city', null);
                                                $set('content.state', null);
                                                $livewire->validateOnly('data.content.postcode');
                                                $cepData = Http::get("https://viacep.com.br/ws/{$state}/json/")
                                                    ->throw()
                                                    ->json();
                                                if (isset($cepData['erro'])) {
                                                    throw ValidationException::withMessages([
                                                        'data.postcode' => __('CEP not Found'),
                                                    ]);
                                                }
                                                $set('content.neighborhood', $cepData['bairro'] ?? null);
                                                $set('content.address', $cepData['logradouro'] ?? null);
                                                $set('content.city', $cepData['localidade'] ?? null);
                                                $set('content.state', $cepData['uf'] ?? null);
                                            })
                                    ),
                                Forms\Components\TextInput::make('content.phone')
                                    ->required()
                                    ->label(__('Phone'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.address')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Address'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.neighborhood')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Neighborhood'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.city')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('City'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.state')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('State'))
                                    ->maxLength(255),
                            ]),
                        Section::make(__('Status & Control'))
                            ->description(_(''))
                            ->columnSpan(1)
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label(__('Status'))
                                    ->helperText(__('Active or disable this Partner'))
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
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('content.phone')
                    ->label(__('Phone'))
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
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
                TernaryFilter::make('is_active')
                    ->default(true)
                    ->label(__('Status'))
                    ->placeholder(__('Show All'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
