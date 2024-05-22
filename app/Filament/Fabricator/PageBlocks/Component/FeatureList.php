<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class FeatureList extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.feature-list')
            ->schema([
                Section::make('Feature List Cards')
                    ->description('Create a list of cards with icon + title + short description')
                    ->collapsed()
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Repeater::make('cards')
                            ->collapsed()
                            ->cloneable()
                            ->columns(3)
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->prefixIcon('heroicon-o-link')
                                    ->maxLength(140),
                                TextInput::make('title')
                                    ->label('Title')
                                    ->columnSpan(2)
                                    ->maxLength(100),
                                Textarea::make('info')
                                    ->label('Info')
                                    ->columnSpanFull()
                                    ->maxLength(200),
                                TextInput::make('link')
                                    ->label('Card Link')
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-o-link')
                                    ->maxLength(140),
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
