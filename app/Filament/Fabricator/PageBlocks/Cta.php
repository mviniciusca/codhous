<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Cta extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('cta')
            ->schema([
                Section::make('CTA')
                    ->description('CTA is a group with a image + text and subtitle + action button')
                    ->icon('heroicon-o-cube')
                    ->collapsed()
                    ->schema([
                        Group::make([
                            TextInput::make('title')
                                ->required()
                                ->label('Title')
                                ->maxLength(50),
                            Textarea::make('subtitle')
                                ->required()
                                ->label('Sub-title')
                                ->maxLength(180),
                            TextInput::make('btn_text')
                                ->required()
                                ->label('Button Text')
                                ->maxLength(50),
                            TextInput::make('btn_url')
                                ->required()
                                ->label('Button URL')
                                ->prefixIcon('heroicon-o-link')
                                ->maxLength(50),
                            FileUpload::make('image')
                                ->image()
                                ->required()
                                ->imageEditor()
                                ->columnSpanFull(),
                        ])
                            ->columns(2)
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
