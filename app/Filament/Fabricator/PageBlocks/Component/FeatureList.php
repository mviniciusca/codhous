<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class FeatureList extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.feature-list')
            ->icon('heroicon-o-cube')
            ->label(__('Feature List'))
            ->schema([
                Section::make(__('Feature List Cards'))
                    ->description(__('Create a list of cards with icon + title + short description'))
                    ->collapsed()
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Section::make(__('Settings'))
                            ->description(__('Settings and configuration of this card\'s section'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->collapsible()
                            ->columns(3)
                            ->schema([
                                Toggle::make('centered')
                                    ->label(__('Align in the Center'))
                                    ->helperText(__('Place the card content align in the center'))
                                    ->inline(false)
                                    ->default(true),
                                Toggle::make('icon_filled')
                                    ->label(__('Icon Background'))
                                    ->helperText(__('Show icon background'))
                                    ->inline(false)
                                    ->default(true),
                                Select::make('grid_view')
                                    ->label(__('Cards per Line'))
                                    ->helperText(__('Show the cards quantity shown per line'))
                                    ->options([
                                        'default' => 3,
                                        '4' => 4,
                                    ])
                                    ->default('default')
                            ]),
                        Repeater::make('cards')
                            ->collapsed()
                            ->cloneable()
                            ->columns(3)
                            ->schema([
                                TextInput::make('icon')
                                    ->label(__('Icon Name'))
                                    ->required()
                                    ->helperText(__('Set the name of Ionicon\'s icon'))
                                    ->prefix('ionicon')
                                    ->maxLength(140),
                                TextInput::make('title')
                                    ->label(__('Card Title'))
                                    ->required()
                                    ->helperText(__('The title of the card'))
                                    ->columnSpan(2)
                                    ->maxLength(100),
                                TextInput::make('link')
                                    ->label(__('Card Link'))
                                    ->required()
                                    ->helperText(__('The link of the card'))
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-o-link')
                                    ->maxLength(140),
                                Textarea::make('info')
                                    ->label(__('Card Short Description'))
                                    ->helperText(__('The description of the card'))
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->maxLength(200),
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
