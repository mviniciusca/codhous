<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class BudgetTool extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('budget-tool')
            ->icon('heroicon-o-currency-dollar')
            ->label(__('Budget Tool'))
            ->schema([
                TextInput::make('title')
                    ->label(__('Section Title'))
                    ->default(__('Request Your Budget'))
                    ->helperText(__('The main heading displayed above the form')),
                TextInput::make('subtitle')
                    ->label(__('Section Subtitle'))
                    ->default(__('Fill in the form below and our team will get back to you within 24 hours'))
                    ->helperText(__('A short description displayed below the title')),
                Select::make('style')
                    ->label(__('Form Style'))
                    ->options([
                        'default' => __('Default'),
                        'card'    => __('Card'),
                        'minimal' => __('Minimal'),
                    ])
                    ->default('default')
                    ->helperText(__('Visual style of the budget form section')),
                Toggle::make('show_icon')
                    ->label(__('Show Icon'))
                    ->default(true)
                    ->helperText(__('Show a decorative icon in the section header')),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}