<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Contact extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.contact')
            ->icon('heroicon-o-cpu-chip')
            ->label('Contact Section')
            ->schema([
                Section::make(__('Core Feature: Contact Section'))
                    ->description(__('Add a Contact Section in your application'))
                    ->icon('heroicon-o-cpu-chip')
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
