<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ButtonGroup extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.button-group')
            ->icon('heroicon-o-cube')
            ->label(__('Button Group'))
            ->schema([
                Section::make('Group Buttons')
                    ->description('A group with a pair of buttons')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->collapsed()
                    ->schema([
                        Repeater::make('buttons')
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('Button Title'))
                                    ->helperText(__('Button Title. Max 80 characters'))
                                    ->maxLength(80),
                                TextInput::make('link')
                                    ->label(__('URL Link'))
                                    ->helperText(__('Button Link. Max 255 characters'))
                                    ->maxLength(255),
                                Toggle::make('target')
                                    ->default(false)
                                    ->label(__('External Link'))
                                    ->helperText(__('When active, opens the link in the new window')),
                                Toggle::make('filled')
                                    ->default(true)
                                    ->label(__('Filled Button Style'))
                                    ->helperText(__('When active, activate the "filled" style.')),
                                TextInput::make('icon')
                                    ->label(__('Button Icon'))
                                    ->prefix('ionicon')
                                    ->helperText(__('Button Icon. Max 255 characters'))
                                    ->maxLength(255),
                                Toggle::make('iconLeft')
                                    ->default(true)
                                    ->label(__('Icon Position'))
                                    ->helperText(__('When active, the icon appears before the text')),
                            ]),
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
