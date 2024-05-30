<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SettingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettingResource\RelationManagers;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditContact::class,
            Pages\EditLayout::class,
        ]);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('App Settings'))
                    ->description(__('Define the Application Global Settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('app_name')
                            ->label(__('Application Name'))
                            ->helperText(__('Define here the Application or Website Name.'))
                            ->maxLength(140)
                            ->required(),
                        TextInput::make('email')
                            ->label(__('Public E-mail'))
                            ->helperText(__('Define here the e-mail address for you or your Company. This is a public information.'))
                            ->maxLength(140)
                            ->required(),
                        TextInput::make('office_hour')
                            ->label(__('Office Hour'))
                            ->helperText(__('Define here the office hour of your Company.'))
                            ->maxLength(140)
                            ->columnSpanFull()
                            ->required(),
                    ]),
                Section::make('SEO & Meta')
                    ->description(__('Define here the search engine optimization with meta tags'))
                    ->icon('heroicon-o-magnifying-glass')
                    ->collapsible()
                    ->columns(2)
                    ->schema([]),
                Section::make('Security and Management')
                    ->description(__('Control the visibility of application'))
                    ->icon('heroicon-o-shield-exclamation')
                    ->collapsible()
                    ->columns(2)
                    ->schema([]),
                Section::make('Modules Control')
                    ->description(__('Control the global module visibility'))
                    ->icon('heroicon-o-eye')
                    ->collapsible()
                    ->columns(2)
                    ->schema([]),
                Section::make('Add-ons & External Complements')
                    ->description(__('Boost the website with complements or scripts from external source'))
                    ->icon('heroicon-o-puzzle-piece')
                    ->collapsible()
                    ->columns(2)
                    ->schema([]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Setting Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //  Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
            'edit_layout' => Pages\EditLayout::route('/{record}/edit-layout'),
            'edit_contact' => Pages\EditContact::route('/{record}/edit-contact'),
        ];
    }
}
