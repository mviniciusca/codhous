<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditNavigation extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    public static function getNavigationLabel(): string
    {
        return __('Menu Navigation');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Menu Navigation Settings');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Navigation Menu'))
                    ->description(__('Define the navigation menu of the application'))
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->relationship('navigation')
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
                Section::make(__('Navigation Button'))
                    ->description(__('Edit the content for the navigation button menu. This appears on side the menu.'))
                    ->icon('heroicon-o-pencil-square')
                    ->collapsible()
                    ->relationship('navigation')
                    ->schema([
                        Repeater::make('nav_button')
                            ->label(__('Navigation Button'))
                            ->cloneable()
                            ->columns(6)
                            ->schema([
                                Toggle::make('status')
                                    ->label(__('Visible'))
                                    ->inline(false)
                                    ->default(true),
                                TextInput::make('title')
                                    ->label(__('Button Title'))
                                    ->columnSpan(2)
                                    ->helperText(__('Define the title of the button')),
                                TextInput::make('link')
                                    ->label(__('Button URL'))
                                    ->prefixIcon('heroicon-o-link')
                                    ->columnSpan(3)
                                    ->helperText(__('Define the link of the button')),
                                TextInput::make('icon')
                                    ->label(__('Button Icon'))
                                    ->columnSpan(3)
                                    ->prefix('ionicon')
                                    ->helperText(__('Choose a Ionicon name for a icon')),
                                Toggle::make('target')
                                    ->label(__('External'))
                                    ->helperText(__('When active, opens the link in a new window'))
                                    ->columnSpan(1)
                                    ->inline(false)
                                    ->default(false),
                                Toggle::make('filled')
                                    ->label(__('Filled'))
                                    ->helperText(__('When active, fill the button\'s background'))
                                    ->columnSpan(1)
                                    ->inline(false)
                                    ->default(true),
                                Toggle::make('iconLeft')
                                    ->label(__('Icon Left'))
                                    ->helperText(__('When active, places the icon before the text'))
                                    ->columnSpan(1)
                                    ->inline(false)
                                    ->default(true),
                            ])
                    ])
            ]);
    }
}
