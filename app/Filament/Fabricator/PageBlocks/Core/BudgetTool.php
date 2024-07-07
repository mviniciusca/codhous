<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use App\Models\Setting;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class BudgetTool extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.budget-tool')
            ->label(__('Budget Tool'))
            ->icon('heroicon-o-shopping-bag')
            ->schema([
                Section::make(__('Core Feature: Budget Tool'))
                    ->description(__('Add a Budget Tool in your Application'))
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsed()
                    ->schema([
                        Hidden::make('id')
                    ])
            ]);
    }
    public static function mutateData(array $data): array
    {
        return $data;
    }
}