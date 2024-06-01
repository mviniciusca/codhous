<?php

namespace App\Filament\Fabricator\PageBlocks\Layout;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Breaker extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('layout.breaker')
            ->label(__('Layout Space Break'))
            ->icon('heroicon-o-paint-brush')
            ->schema([
                Section::make(__('Section Breaker'))
                    ->description(__('Add a space between sections'))
                    ->icon('heroicon-o-view-columns')
                    ->collapsed()
                    ->schema([
                        Select::make('padding')
                            ->label(__('Vertical Space'))
                            ->helperText(__('Choose the size of the space between the sections. The default value is medium py-4'))
                            ->options([
                                'small' => __('Small py-4'),
                                'medium' => __('Medium py-8'),
                                'large' => __('Large py-12'),
                                'extra-large' => __('Extra Large py-24')
                            ])
                            ->default('small'),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
