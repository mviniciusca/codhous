<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;
use Illuminate\Contracts\Support\Htmlable;

class EditAddons extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    public static function getNavigationLabel(): string
    {
        return __('Add-ons');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Add-ons & External Complements Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Add-ons & External Complements'))
                    ->description(__('Boost the website with complements or scripts from external source'))
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Textarea::make('header_scripts')
                            ->label(__('Header Scripts'))
                            ->helperText(__('Define here the of the application. This section is shown before opening the body tag'))
                            ->rows(3)
                            ->maxLength(2000),
                        Textarea::make('body_scripts')
                            ->label(__('Body Scripts'))
                            ->helperText(__('Define here the of the application. This section is shown before closing the body tag'))
                            ->rows(3)
                            ->maxLength(2000),
                        Textarea::make('google_analytics')
                            ->label(__('Google Analytics'))
                            ->helperText(__('Paste here the entire Google Analytics Codes'))
                            ->rows(3)
                            ->maxLength(2000),
                        Textarea::make('google_tag')
                            ->label(__('Google Tag'))
                            ->helperText(__('Paste here the entire Google Tag Code'))
                            ->rows(3)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
