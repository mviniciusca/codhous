<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    public static function getNavigationLabel(): string
    {
        return __('Layout & Appearance');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Layout & Appearance'))
                    ->description(__('Define the Appearance and Layout of the Application'))
                    ->icon('heroicon-o-paint-brush')
                    ->relationship('layout')
                    ->collapsible()
                    ->columns(5)
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('App Logo'))
                            ->image()
                            ->imageEditor()
                            ->directory('layout')
                            ->helperText(__('Upload the application logo')),
                        FileUpload::make('favicon')
                            ->label(__('App Favicon'))
                            ->image()
                            ->imageEditor()
                            ->directory('layout')
                            ->helperText(__('Upload the application favicon')),
                        FileUpload::make('background_image')
                            ->label(__('Background Image'))
                            ->image()
                            ->columnSpan(3)
                            ->imageEditor()
                            ->directory('layout')
                            ->helperText(__('Upload the application background image')),
                    ])
            ]);
    }
}
