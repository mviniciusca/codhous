<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditSocial extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-share';
    public static function getNavigationLabel(): string
    {
        return __('Social Network');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Edit Social Network');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Social Network'))
                    ->icon('heroicon-o-link')
                    ->description(__('Manage your social network accounts'))
                    ->schema([
                        Repeater::make('social')
                            ->label(__('Social Network'))
                            ->columns(6)
                            ->schema([
                                Toggle::make('status')
                                    ->label(__('Active'))
                                    ->helperText(__('Visible for Public'))
                                    ->default(true)
                                    ->inline(false),
                                TextInput::make('social_network')
                                    ->columnSpan(2)
                                    ->lazy()
                                    ->debounce(250)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('icon', Str::slug($state)))
                                    ->helperText(__('ex: Facebook'))
                                    ->label(__('Social Network')),
                                TextInput::make('link')
                                    ->columnSpan(2)
                                    ->helperText(__('ex: https://www.facebook.com'))
                                    ->label(__('Link')),
                                TextInput::make('icon')
                                    ->lazy()
                                    ->dehydrated()
                                    ->readonly()
                                    ->columnSpan(1)
                                    ->helperText(__('Automatic'))
                                    ->label(__('Icon')),
                            ]),
                    ]),
            ]);
    }
}
