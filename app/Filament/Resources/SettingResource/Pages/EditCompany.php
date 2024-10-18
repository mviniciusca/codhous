<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Str;
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
                        TextInput::make('address.postcode')
                            ->required()
                            ->maxLength(255)
                            ->label(__('CEP'))
                            ->mask('99.999-999'),
                        TextInput::make('address.street')
                            ->required()
                            ->maxLength(255)
                            ->label(__('Street')),
                        TextInput::make('address.number')
                            ->maxLength(255)
                            ->label(__('Number (optional)')),
                        TextInput::make('address.neighborhood')
                            ->required()
                            ->maxLength(255)
                            ->label(__('Neighborhood')),
                        TextInput::make('address.city')
                            ->required()
                            ->maxLength(255)
                            ->label(__('City')),
                        TextInput::make('address.state')
                            ->live()
                            ->debounce(500)
                            ->required()
                            ->maxLength(2)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                return $set('address.state', Str::upper($state));
                            })
                            ->label(__('UF')),
                    ]),
                Section::make(__('Budget / Invoice Document Settings'))
                    ->icon('heroicon-o-document')
                    ->description(__('Define information that will be displayed at the end of each Budget or Invoice Document.'))
                    ->relationship('company')
                    ->columns(2)
                    ->schema([
                        Textarea::make('budget_information')
                            ->label(__('Information (optional)'))
                            ->rows(5)
                            ->maxLength(300)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
