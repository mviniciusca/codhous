<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use App\Models\Setting;
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

    protected static ?string $navigationGroup = 'Customers & Partners';

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
                        Section::make(__('Partner'))
                            ->icon('heroicon-o-cube')
                            ->description(__('Add or control your partner'))
                            ->columnSpan(4)
                            ->columns(3)
                            ->schema([
                                Forms\Components\Hidden::make('setting_id')
                                    ->default(Setting::first()->id)
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label(__('Company'))
                                    ->lazy()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->maxLength(255),
                                Forms\Components\Hidden::make('slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Slug')),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label(__('Email'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.phone')
                                    ->required()
                                    ->helperText(__('Phone Number'))
                                    ->tel()
                                    ->mask('(99)99999-9999')
                                    ->placeholder(_('(xx) XXXX-XXXX'))
                                    ->helperText(__('Your phone with local area'))
                                    ->label(__('Phone')),
                                Forms\Components\TextInput::make('postcode')
                                    ->required()
                                    ->minLength(9)
                                    ->mask('99999-9999')
                                    ->placeholder('22022-000')
                                    ->maxLength(9)
                                    ->helperText(__('Postcode'))
                                    ->required()
                                    ->label(__('Postcode'))
                                    ->suffixAction(
                                        fn ($state, $set, $livewire) => Action::make('search-action')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(function () use ($state, $livewire, $set) {
                                                $livewire->validateOnly('content.data.postcode');
                                                $postcode = new PostcodeFinder($state, $set);
                                                $postcode->find();
                                            })
                                    ),
                                Forms\Components\TextInput::make('content.address')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Street'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.number')
                                    ->label(__('Number'))
                                    ->columnSpan(1)
                                    ->maxLength(140),
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
            'index'  => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit'   => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
