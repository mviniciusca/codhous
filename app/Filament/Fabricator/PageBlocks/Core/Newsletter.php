<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Newsletter extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.newsletter')
            ->schema([
                Section::make(_('Core Feature: Newsletter Module'))
                    ->icon('heroicon-o-envelope')
                    ->collapsed()
                    ->description(__('Add a E-mail catcher in your application'))
                    ->schema([
                        Toggle::make('status')
                            ->inline(true)
                            ->label(__('Show this Module'))
                            ->default(true)
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
