<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class EditWhatsapp extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Whatsapp Integration'))
                    ->description(__('Edit your company whatsapp settings'))
                    ->columns(2)
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Toggle::make('whatsapp.status')
                            ->label(__('Active'))
                            ->helperText(__('Enable or disable this section. This is a global action.'))
                            ->inline(false)
                            ->default(true),
                        TextInput::make('whatsapp.phone')
                            ->label(__('Whatsapp Phone Number'))
                            ->helperText(__('Define here your phone number of your Whatsapp Account')),
                        Textarea::make('whatsapp.message')
                            ->label(__('Callout Message'))
                            ->placeholder(__('Example: How Can I assist you?'))
                            ->helperText(__('Set the callout message that your customer will send to you.'))
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
                Section::make(__('Design & Layout'))->description(__('Edit your company whatsapp settings'))
                    ->columns(2)
                    ->icon('heroicon-o-photo')
                    ->schema([])
            ]);
    }
}
