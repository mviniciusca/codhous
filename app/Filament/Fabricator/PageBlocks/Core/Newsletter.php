<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Newsletter extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.newsletter')
            ->schema([
                //
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}