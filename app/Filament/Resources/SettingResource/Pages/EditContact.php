<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class EditContact extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    public static function getNavigationLabel(): string
    {
        return __('Contact & Locale');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Visibility Control'))
                    ->icon('heroicon-o-eye')
                    ->relationship('contact')
                    ->description(__('Control the public visibility of this section.'))
                    ->schema([
                        Toggle::make('status')
                            ->inline(false)
                    ]),
                Section::make(__('Contact & Locale'))
                    ->description(__('Edit your address and localization settings'))
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->schema([
                        Group::make()
                            ->columns(5)
                            ->relationship('contact')
                            ->schema([
                                Select::make('address_name')
                                    ->label(__('Address Type'))
                                    ->required()
                                    ->options([
                                        'main' => __('Main'),
                                        'company' => __('Company'),
                                        'office' => __('Office'),
                                    ])
                                    ->helperText(__('Define here the type of your address. This is a public choice.')),
                                TextInput::make('address')
                                    ->columnSpan(4)
                                    ->required()
                                    ->label(__('Public Address'))
                                    ->helperText(__('Your full address')),
                                Textarea::make('map')
                                    ->columnSpanFull()
                                    ->required()
                                    ->placeholder('<iframe src=" ... ')
                                    ->label(__('Google Maps Code'))
                                    ->helperText(__('Paste here only https code inside after src. Example: <iframe src=""></iframe> from your Google Maps localization'))
                                    ->rows(5),
                            ])
                    ]),

            ]);
    }
}
