<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    public static function getNavigationLabel(): string
    {
        return __('Budget Tool');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Options'))
                    ->description(__('Change the content for your budget tool'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(2)
                    ->schema([
                        TagsInput::make('budget.fck')
                            ->label(__('FCK'))
                            ->required()
                            ->helperText(__('Enter the values of FCK that your company works')),
                        TagsInput::make('budget.type')
                            ->label(__('Type'))
                            ->required()
                            ->helperText(__('Enter the type of concrete that your company works')),
                        TagsInput::make('budget.area')
                            ->label(__('Local / Area'))
                            ->required()
                            ->helperText(__('Enter the local or area that your company works')),
                    ]),
                Section::make(__('Pricing'))
                    ->description(__('Define the pricing of the meter cubic (m³) for your concrete'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(4)
                    ->schema([
                        TextInput::make('budget.price')
                            ->label(__('Price'))
                            ->regex('/^[0-9]/')
                            ->prefix(__('U$'))
                            ->required()
                            ->suffix(', 00')
                            ->helperText(__('Integer value')),
                        TextInput::make('budget.tax')
                            ->label(__('Tax (Optional)'))
                            ->regex('/^[0-9]/')
                            ->prefix(__('U$'))
                            ->suffix(', 00')
                            ->helperText(__('Integer value')),
                    ]),
            ]);
    }
}
