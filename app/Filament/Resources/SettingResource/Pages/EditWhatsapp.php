<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditWhatsapp extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function getNavigationLabel(): string
    {
        return __('Whatsapp Web Chat');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Whatsapp Web Chat Settings');
    }

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
                            ->prefixIcon('heroicon-o-phone')
                            ->required()
                            ->prefix('+'.env('COUNTRY_CODE'))
                            ->tel()
                            ->maxLength(14)
                            ->mask('(99)99999-9999')
                            ->helperText(__('Define here your phone number of your Whatsapp Account. Only numbers')),
                        Textarea::make('whatsapp.message')
                            ->label(__('Callout Message'))
                            ->placeholder(__('Example: I wanna a budget for your service.'))
                            ->helperText(__('Set the callout message that your customer will send to you from your website. Max.: 200 characters'))
                            ->columnSpanFull()
                            ->maxLength(200)
                            ->rows(3),
                    ]),
                Section::make(__('Design & Layout'))
                    ->description(__('Edit your company whatsapp settings'))
                    ->columns(2)
                    ->icon('heroicon-o-photo')
                    ->schema([
                        ColorPicker::make('whatsapp.color')
                            ->default('#25d366')
                            ->helperText(__('Default color: #25d366')),
                        TextInput::make('whatsapp.icon')
                            ->prefixIcon('heroicon-o-cube')
                            ->helperText(__('Ionicon\'s icon name. Default is logo-whatsapp')),

                    ]),
            ]);
    }
}
