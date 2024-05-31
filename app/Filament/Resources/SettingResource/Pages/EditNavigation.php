<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditNavigation extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    public static function getNavigationLabel(): string
    {
        return __('Menu Navigation');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Navigation Menu'))
                    ->description(__('Define the navigation menu of the application'))
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->collapsible()
                    ->schema([
                        Repeater::make('navigation')
                            ->columns(3)
                            ->cloneable()
                            ->schema([
                                TextInput::make('menu_title')
                                    ->label(__('Menu Title'))
                                    ->required()
                                    ->helperText(__('Title of the Menu')),
                                TextInput::make('menu_url')
                                    ->label(__('Menu URL'))
                                    ->required()
                                    ->prefixIcon('heroicon-o-link')
                                    ->url()
                                    ->helperText(__('The URL of the menu')),
                                Select::make('target')
                                    ->label(__('Link Target'))
                                    ->helperText(__('Define if the link it will open in the same window or a new'))
                                    ->options([
                                        'self' => __('Self'),
                                        'blank' => __('Blank'),
                                    ])
                                    ->default('self'),
                            ]),
                    ]),
            ]);
    }
}
