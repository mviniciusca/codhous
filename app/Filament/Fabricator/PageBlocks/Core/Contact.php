<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Section;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Contact extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.contact')
            ->icon('heroicon-o-cpu-chip')
            ->label(__('Contact Section'))
            ->schema([
                Section::make(__('Contact Section'))
                    ->description(__('Core Feature for a Contact Section. This can be edited on Settings > Contact & Locale'))
                    ->icon('heroicon-o-cpu-chip')
                    ->collapsed()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
