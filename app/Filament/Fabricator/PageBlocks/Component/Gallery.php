<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Gallery extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.gallery')
            ->icon('heroicon-o-cube')
            ->label(__('Image Gallery'))
            ->schema([
                Section::make('Gallery Settings')
                    ->description('Gallery Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->schema([
                        Group::make()
                            ->schema([
                                Fieldset::make('Display on this Gallery')
                                    ->columns(4)
                                    ->schema([
                                        Toggle::make('only_images')
                                            ->inline(false)
                                            ->default(false)
                                            ->label('Only Images'),
                                        Toggle::make('only_title')
                                            ->inline(false)
                                            ->default(true)
                                            ->label('Title'),
                                        Toggle::make('only_subtitle')
                                            ->inline(false)
                                            ->default(true)
                                            ->label('Subtitle'),
                                        Toggle::make('only_info')
                                            ->inline(false)
                                            ->default(true)
                                            ->label('Info')
                                    ]),
                                Fieldset::make('Gallery Grid')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('col_size')
                                                    ->label('Items per View')
                                                    ->helperText('Show how many items you want to see in the gallery\'s row')
                                                    ->options([
                                                        '2' => '2',
                                                        '3' => '3',
                                                        '4' => '4',
                                                        '5' => '5',
                                                        '6' => '6',
                                                    ])
                                                    ->default('4')
                                            ]),
                                    ])
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
                                                    ->maxLength(100),
                                                TextInput::make('link')
                                                    ->prefixIcon('heroicon-o-link')
                                                    ->label('Link')
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
