<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

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
                    ->description(__('Control the visibility of application'))
                    ->icon('heroicon-o-shield-exclamation')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->helperText(__('Active or disable the maintenance mode of the application'))
                            ->label(__('Maintenance Mode'))
                            ->inline(),
                        Toggle::make('discovery_mode')
                            ->helperText(__('See the application when Maintenance Mode is active'))
                            ->label(__('Discovery Mode'))
                            ->inline(),
                    ]),
                Section::make(__('Maintenance Page Design'))
                    ->description(__('Configure the maintenance page design layout'))
                    ->icon('heroicon-o-paint-brush')
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->helperText(__('')),
                        Textarea::make('message')
                            ->label(__('Message'))
                            ->required()
                            ->helperText(__('')),
                        FileUpload::make('image')
                            ->image()
                            ->columnSpanFull()
                            ->directory('maintenance')
                            ->label(__('Image Upload')),
                    ]),
            ]);
    }
}
