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
                    ->schema([
                        TagsInput::make('budget.fck')
                            ->label(__('FCK'))
                            ->helperText(__('Enter the values of FCK that your company works')),
                        TagsInput::make('budget.type')
                            ->label(__('Type'))
                            ->helperText(__('Enter the type of FCK that your company works')),
                        TagsInput::make('budget.local_area')
                            ->label(__('Type'))
                            ->helperText(__('Enter the type of FCK that your company works')),
                    ]),
            ]);
    }
}
