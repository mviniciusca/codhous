<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
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
                        Group::make()
                            ->columns(4)
                            ->schema([
                                Toggle::make('only_images')
                                    ->inline(false)
                                    ->label('Display only Images'),
                                Toggle::make('only_title')
                                    ->inline(false)
                                    ->label('Display only title'),
                                Toggle::make('only_subtitle')
                                    ->inline(false)
                                    ->label('Display only subtitle'),
                                Toggle::make('only_info')
                                    ->inline(false)
                                    ->label('Display only info')
                            ]),

                    ]),
                Section::make('Image Gallery')
                    ->description('Add a new Image Gallery. Use same size images')
                    ->icon('heroicon-o-photo')
                    ->collapsed()
                    ->schema([
                        Repeater::make('images')
                            ->columns(6)
                            ->cloneable()
                            ->collapsed()
                            ->schema([
                                FileUpload::make('image')
                                    ->columnSpan(2)
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
                                Group::make()
                                    ->columnSpan(4)
                                    ->schema([
                                        Group::make()
                                            ->schema([
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
                                            ])
                                    ]),
                            ])
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
