<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;

class EditSocial extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Social Network'))
                    ->icon('heroicon-o-link')
                    ->description(__('Manage your social network accounts'))
                    ->schema([
                        Repeater::make('social')
                            ->columns(6)
                            ->schema([
                                Toggle::make('status')
                                    ->label(__('Active'))
                                    ->helperText(__('Visible for Public'))
                                    ->default(true)
                                    ->inline(false),
                                TextInput::make('social_network')
                                    ->columnSpan(1)
                                    ->helperText(__('ex: Facebook'))
                                    ->label(__('Social Network')),
                                TextInput::make('link')
                                    ->columnSpan(2)
                                    ->prefix('https://')
                                    ->helperText(__('ex: facebook.com'))
                                    ->label(__('Link')),
                                TextInput::make('icon')
                                    ->prefix('name')
                                    ->columnSpan(2)
                                    ->helperText(__('Use ionicons for name'))
                                    ->label(__('Icon (Ionicon Name)')),

                            ]),
                    ]),
            ]);
    }
}
