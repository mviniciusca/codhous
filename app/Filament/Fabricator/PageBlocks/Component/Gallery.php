<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Gallery extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.gallery')
            ->schema([
                Section::make('Gallery Settings')
                    ->description('Gallery Settings')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Toggle::make('only_images')
                            ->inline(false)
                            ->label('Display only Images')
                    ]),
                Section::make('Image Gallery')
                    ->description('Add a new Image Gallery. Use same size images')
                    ->icon('heroicon-o-photo')
                    ->collapsed()
                    ->schema([
                        Repeater::make('images')
                            ->columns(2)
                            ->cloneable()
                            ->collapsed()
                            ->schema([
                                FileUpload::make('image')
                                    ->columnSpanFull()
                                    ->label('Image Upload')
                                    ->directory('gallery')
                                    ->helperText('Image should be 16:9 aspect ratio')
                                    ->image()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->imageEditor(),
                                TextInput::make('title')
                                    ->label('Title')
                                    ->maxLength(30),
                                TextInput::make('subtitle')
                                    ->label('Subtitle')
                                    ->maxLength(30),
                                Textarea::make('info')
                                    ->label('Info')
                                    ->rows(2)
                                    ->maxLength(100)
                                    ->columnSpanFull()
                            ])
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
