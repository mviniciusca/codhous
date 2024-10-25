<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\FileUpload;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\SettingResource;

class EditBudget extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    public static function getNavigationLabel(): string
    {
        return __('Budget Tool');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Budget Tool Settings');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Visibility Control'))
                    ->icon('heroicon-o-eye')
                    ->description(__('Control the public visibility of this section.'))
                    ->schema([
                        Toggle::make('budget_is_active')
                            ->label(__('Active'))
                            ->inline()
                            ->helperText(__('Enable or disable this section. This is a global action.'))
                    ]),

                Section::make(__('Design'))
                    ->description(__('Change the design for your budget tool'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('budget_image')
                            ->image()
                            ->imageEditor()
                            ->directory('budget')
                            ->label(__('Image Upload'))
                            ->helperText(__('Upload a image for your budget tool'))
                    ]),
            ]);
    }
}
