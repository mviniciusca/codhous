<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;

class EditCompany extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    public static function getNavigationLabel(): string
    {
        return __('Company Information');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Company Information'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('trade_name')
                            ->helperText(__(''))
                            ->required()
                            ->maxLength(255)
                            ->label(__('Company Name')),
                        TextInput::make('legal_name')
                            ->helperText(__(''))
                            ->required()
                            ->maxLength(255)
                            ->label(__('Company Legal Name / Trade Name')),
                        TextInput::make('phone')
                            ->helperText(__(''))
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->prefix('+' . env('COUNTRY_CODE'))
                            ->mask('(99) 9999-9999')
                            ->label(__('Phone Number')),
                        TextInput::make('email')
                            ->helperText(__(''))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label(__('Email Address')),
                        TextInput::make('cnpj')
                            ->required()
                            ->maxLength(255)
                            ->mask('99.999.999/9999-99')
                            ->helperText(__(''))
                            ->label(__('CNPJ'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Company Address'))
                    ->icon('heroicon-o-map')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address.postcode'),
                        TextInput::make('address.street'),
                        TextInput::make('address.number'),
                        TextInput::make('address.neighborhood'),
                        TextInput::make('address.city'),
                        TextInput::make('address.state'),
                    ]),
                Section::make(__('Budget / Invoice Document Settings'))
                    ->icon('heroicon-o-document')
                    ->description(__('Define information that will be displayed at the end of each Budget or Invoice Document.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        Textarea::make('budget_information')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
