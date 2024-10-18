<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;

class EditCompany extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Section::make(__('Company Settings'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('setting')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('email'),
                        TextInput::make('phone'),
                    ]),
            ]);
    }
}
