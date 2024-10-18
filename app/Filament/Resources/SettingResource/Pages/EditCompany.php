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
                Section::make(__('Company Information'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('trade_name'),
                        TextInput::make('legal_name'),
                        TextInput::make('phone'),
                        TextInput::make('email'),
                        TextInput::make('cnpj')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Company Address'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address.postcode'),
                        TextInput::make('address.street'),
                        TextInput::make('address.number'),
                        TextInput::make('address.city'),
                        TextInput::make('address.state'),
                    ]),
            ]);
    }
}
