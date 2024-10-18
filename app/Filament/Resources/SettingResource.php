<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditSetting::class,
            Pages\EditCompany::class,
            Pages\EditNavigation::class,
            Pages\EditLayout::class,
            Pages\EditContact::class,
            Pages\EditSocial::class,
            Pages\EditBudget::class,
            Pages\EditWhatsapp::class,
            Pages\EditAddons::class,
            Pages\EditMaintenance::class,
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
                            ->prefixIcon('heroicon-o-cube')
                            ->label(__('Application Name'))
                            ->helperText(__('Define here the Application or Website Name.'))
                            ->maxLength(140)
                            ->required(),
                        TextInput::make('email')
                            ->label(__('Public E-mail'))
                            ->helperText(__('Define here the e-mail address for you or your Company. This is a public information.'))
                            ->maxLength(140)
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label(__('Public Phone'))
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->prefix('+' . env('COUNTRY_CODE'))
                            ->mask('(99)9999-9999')
                            ->helperText(__('Define here the phone number for you or your Company. This is a public information.'))
                            ->maxLength(15)
                            ->tel()
                            ->required(),
                        TextInput::make('office_hour')
                            ->prefixIcon('heroicon-o-clock')
                            ->label(__('Office Hour'))
                            ->helperText(__('Define here the office hour of your Company.'))
                            ->maxLength(150)
                            ->required(),
                    ]),
                Section::make(__('Modules Control'))
                    ->description(__('Control the global module visibility'))
                    ->icon('heroicon-o-eye')
                    ->relationship('module')
                    ->collapsible()
                    ->schema([
                        Group::make()
                            ->columns(5)
                            ->schema([
                                Toggle::make('module.header')
                                    ->label(__('Header')),
                                Toggle::make('module.contact')
                                    ->label(__('Contact')),
                                Toggle::make('module.newsletter')
                                    ->label(__('Newsletter')),
                                Toggle::make('module.budget')
                                    ->label(__('Budget Tool')),
                                Toggle::make('module.footer')
                                    ->label(__('Footer'))
                            ])
                    ]),
                Section::make(__('SEO & Meta'))
                    ->description(__('Define here the search engine optimization with meta tags'))
                    ->icon('heroicon-o-magnifying-glass')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('Title'))
                            ->helperText(__('Define here the title of the application'))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('meta_author')
                            ->label(__('Author'))
                            ->helperText(__('Define here the author of the application'))
                            ->maxLength(255)
                            ->required(),
                        Textarea::make('meta_keywords')
                            ->label(__('Keywords'))
                            ->helperText(__('Define here the keywords of the application'))
                            ->maxLength(255)
                            ->rows(3)
                            ->required(),
                        Textarea::make('meta_description')
                            ->label(__('Description'))
                            ->helperText(__('Define here the description of the application'))
                            ->maxLength(255)
                            ->rows(3)
                            ->required(),
                    ]),
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
            'edit_navigation' => Pages\EditNavigation::route('/{record}/edit-navigation'),
            'edit_social' => Pages\EditSocial::route('/{record}/edit-social'),
            'edit_budget' => Pages\EditBudget::route('/{record}/edit-budget'),
            'edit_whatsapp' => Pages\EditWhatsapp::route('/{record}/edit-whatsapp'),
            'edit_addons' => Pages\EditAddons::route('/{record}/edit-addons'),
            'edit_maintenance' => Pages\EditMaintenance::route('/{record}/edit-maintenance'),
            'edit_company' => Pages\EditCompany::route('/{record}/edit-company'),
        ];
    }
}
