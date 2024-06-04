<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Cta extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.cta')
            ->icon('heroicon-o-cube')
            ->label(__('CTA'))
            ->schema([
                Section::make('CTA')
                    ->description('CTA is a group with a image + text and subtitle + action button')
                    ->icon('heroicon-o-stop')
                    ->collapsed()
                    ->schema([
                        Fieldset::make(__('CTA Module Settings'))
                            ->columns(2)
                            ->schema([
                                Toggle::make('status')
                                    ->default(true)
                                    ->label(__('Show Module'))
                                    ->helperText(__('Control the public visibility of this module'))
                                    ->inline(false),
                                Toggle::make('axis')
                                    ->default(true)
                                    ->label(__('Invert Position'))
                                    ->helperText(__('Invert the position of the CTA components'))
                                    ->inline(false),
                            ]),
                        Group::make([
                            TextInput::make('title')
                                ->required()
                                ->label(__('Title'))
                                ->columnSpanFull()
                                ->maxLength(140),
                            Textarea::make('subtitle')
                                ->required()
                                ->columnSpanFull()
                                ->label(__('Subtitle'))
                                ->maxLength(240),
                            TextInput::make('btn_text')
                                ->required()
                                ->label(__('Button Text'))
                                ->maxLength(140),
                            TextInput::make('btn_url')
                                ->label(__('Button URL (Optional)'))
                                ->prefixIcon('heroicon-o-link')
                                ->maxLength(250),
                            Toggle::make('target')
                                ->label(__('External Link'))
                                ->helperText(__('Open this link in a new window'))
                                ->default(false),
                            FileUpload::make('image')
                                ->label(__('Upload Image'))
                                ->image()
                                ->directory('cta')
                                ->required()
                                ->imageEditor()
                                ->imageCropAspectRatio('16:9')
                                ->helperText(__('This will automatically crop into a 16:9 ratio format'))
                                ->columnSpanFull(),
                        ])
                            ->columns(3)
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
