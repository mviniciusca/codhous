<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                Section::make(__('Visibility'))
                    ->icon('heroicon-o-eye')
                    ->description(__('Control the visibility of this section. This is a global action'))
                    ->schema([
                        Toggle::make('budget.is_active')
                            ->label(__('Active'))
                            ->inline()
                            ->helperText(__('Enable or disable this section. This affects every page that this tool is on it'))
                    ]),
                Section::make(__('Options'))
                    ->description(__('Change the content for your budget tool'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TagsInput::make('budget.fck')
                            ->label(__('FCK'))
                            ->required()
                            ->placeholder(__('press enter to add'))
                            ->helperText(__('Enter the values of FCK that your company works')),
                        TagsInput::make('budget.type')
                            ->label(__('Type'))
                            ->placeholder(__('press enter to add'))
                            ->required()
                            ->helperText(__('Enter the type of concrete that your company works')),
                        TagsInput::make('budget.area')
                            ->label(__('Local / Area'))
                            ->placeholder(__('press enter to add'))
                            ->required()
                            ->helperText(__('Enter the local or area that your company works')),
                    ]),
            ]);
    }
}
