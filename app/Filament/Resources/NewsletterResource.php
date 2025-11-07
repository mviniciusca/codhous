<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterOverwview;
use App\Models\Newsletter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $slug = 'subscribers';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communication';

    public static function getNavigationBadge(): ?string
    {
        if (static::getModel()::count() != 0) {
            return static::getModel()::count();
        }

        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('Subscribers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Toggle::make('is_active')
                    ->label(__('Status'))
                    ->helperText(__('Subscriber status'))
                    ->columnSpan(1)
                    ->inline(false),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->helperText(__('Subscriber username'))
                    ->columnSpan(3)
                    ->disabled(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->helperText(__('Subscriber email'))
                    ->columnSpan(2)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('email')
                    ->label(__('Email')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Status'))
                    ->placeholder(__('Show All'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive'))
                    ->default(true),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
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

    public static function getWidgets(): array
    {
        return [
            NewsletterOverwview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit'   => Pages\EditNewsletter::route('/{record}/edit'),
            'bin'    => Pages\SubscriberBin::route('/bin'),
        ];
    }
}
