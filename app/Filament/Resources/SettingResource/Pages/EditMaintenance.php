<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;

class EditMaintenance extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
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
        ]);
    }
}
