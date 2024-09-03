<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\FileUpload;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\SettingResource;

class EditMaintenance extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    public static function getNavigationLabel(): string
    {
        return __('Security & Management');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Security & Management Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Security & Management'))
                    ->description(__('Control the visibility of application.'))
                    ->icon('heroicon-o-shield-exclamation')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->live()
                            ->helperText(__('Active or disable the maintenance mode of the application.'))
                            ->label(__('Maintenance Mode'))
                            ->afterStateUpdated(fn(Set $set): bool => $set('discovery_mode', false))
                            ->inline(),
                        Toggle::make('discovery_mode')
                            ->visible(fn(Get $get): bool => $get('maintenance_mode'))
                            ->helperText(__('See the application when Maintenance Mode is active.'))
                            ->label(__('Discovery Mode'))
                            ->inline(),
                    ]),
                Section::make(__('Maintenance Mode Page Design'))
                    ->description(__('Configure the maintenance page design layout.'))
                    ->icon('heroicon-o-paint-brush')
                    ->collapsible()
                    ->relationship('layout')
                    ->schema([
                        Group::make()
                            ->schema([
                                Toggle::make('maintenance.show')
                                    ->label(__('Social Network Module'))
                                    ->helperText(__('Show social network links if they available'))
                                    ->inline(true)
                                    ->default(true),
                                TextInput::make('maintenance.title')
                                    ->label(__('Title'))
                                    ->required()
                                    ->placeholder(__('Maintenance Mode'))
                                    ->helperText(__('Define the main tittle of the maintenance mode page.')),
                                Textarea::make('maintenance.message')
                                    ->label(__('Message'))
                                    ->required()
                                    ->placeholder(__('This page is under maintenance mode. Please, come back soon! Thanks.'))
                                    ->helperText(__('Define the main message of the maintenance mode page.')),
                                FileUpload::make('maintenance.image')
                                    ->image()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload the image for maintenance page.'))
                                    ->directory('maintenance')
                                    ->label(__('Image Upload')),
                            ]),
                    ]),
            ]);
    }
}
