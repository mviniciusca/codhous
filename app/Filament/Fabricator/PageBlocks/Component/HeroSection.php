<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class HeroSection extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.hero-section')
            ->icon('heroicon-o-cube')
            ->label(__('Hero Section'))
            ->schema([
                Section::make('Hero Section')
                    ->description('Hero Section Settings')
                    ->icon('heroicon-o-bolt')
                    ->collapsed()
                    ->schema([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->placeholder('Title goes here')
                                    ->label('Title of Hero Section')
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('subtitle')
                                    ->required()
                                    ->placeholder('Subtitle')
                                    ->label('Subtitle of Hero Section')
                                    ->maxLength(140),
                                FileUpload::make('HeroSectionImage')
                                    ->required()
                                    ->columnSpanFull()
                                    ->directory('hero-section')
                                    ->image()
                                    ->imageEditor(),
                                TextInput::make('btn_text')
                                    ->required()
                                    ->placeholder('Button Text')
                                    ->label('Button Text')
                                    ->maxLength(50),
                                TextInput::make('btn_url')
                                    ->required()
                                    ->placeholder('Button Link')
                                    ->label('Button Link')
                                    ->prefixIcon('heroicon-o-link')
                                    ->maxLength(50),
                                TextInput::make('btn_full_text')
                                    ->required()
                                    ->placeholder('Button Text')
                                    ->label('Button Text')
                                    ->maxLength(50),
                                TextInput::make('btn_full_url')
                                    ->required()
                                    ->placeholder('Button Link')
                                    ->prefixIcon('heroicon-o-link')
                                    ->label('Button Link')
                                    ->maxLength(50),
                            ])
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
