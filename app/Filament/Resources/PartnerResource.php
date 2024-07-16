<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Filament\Resources\PartnerResource\RelationManagers;
use App\Models\Partner;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                                    ->label(__(''))
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->label(__(''))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->label(__(''))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postcode')
                                    ->required()
                                    ->label(__(''))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.phone')
                                    ->required()
                                    ->label(__(''))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.address')
                                    ->required()
                                    ->label(__(''))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.city')
                                    ->required()
                                    ->label(__('City'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.state')
                                    ->required()
                                    ->label(__(''))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('content.country')
                                    ->required()
                                    ->label(__(''))
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
