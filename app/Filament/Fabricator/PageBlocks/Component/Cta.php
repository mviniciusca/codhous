<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Cta extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.cta')
            ->icon('heroicon-o-bolt')
            ->label(__('CTA'))
            ->schema([
                Section::make('CTA')
                    ->description('CTA is a group with a image + text and subtitle + action button')
                    ->icon('heroicon-o-bolt')
                    ->collapsed()
                    ->schema([
                        Repeater::make('content')
                            ->label(__('Content'))
                            ->schema([
                                Section::make(__('Content'))
                                    ->collapsible()
                                    ->icon('heroicon-o-pencil')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('title')
                                                    ->required()
                                                    ->label(__('Title'))
                                                    ->helperText(__('Title for Content. Max 200 characters'))
                                                    ->maxLength(200),
                                                Textarea::make('subtitle')
                                                    ->rows(3)
                                                    ->label(__('Subtitle'))
                                                    ->helperText(__('Short description about the content. Max 300 characters'))
                                                    ->maxLength(300),
                                                FileUpload::make('image')
                                                    ->label(__('Upload Image'))
                                                    ->image()
                                                    ->directory('cta')
                                                    ->required()
                                                    ->imageEditor()
                                                    ->imageCropAspectRatio('16:9')
                                                    ->helperText(__('This will automatically crop into a 16:9 ratio format')),
                                            ])
                                    ]),
                                Section::make(__('Button'))
                                    ->collapsible()
                                    ->icon('heroicon-o-cube')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('btn_text')
                                            ->label(__('Button Text (Optional)'))

                                            ->helperText(__('This show the button on page. Max 140 characters'))
                                            ->maxLength(140),
                                        TextInput::make('btn_url')
                                            ->label(__('Button URL (Optional)'))

                                            ->helperText(__('Link for the button. Max 255 characters'))
                                            ->prefixIcon('heroicon-o-link')
                                            ->maxLength(250),
                                        Toggle::make('target')
                                            ->label(__('External Link'))
                                            ->helperText(__('Open this link in a new window'))
                                            ->inline(false)
                                            ->default(false),
                                        Toggle::make('iconLeft')
                                            ->label(__('Icon Position'))
                                            ->helperText(__('Place the icon before or after the text. Active is on the left.'))
                                            ->inline(false)
                                            ->default(true),
                                        TextInput::make('icon')
                                            ->label(__('Ionicon name'))
                                            ->helperText(__('Use a icon name from Ionicon. Ex.: bulb-outline'))
                                            ->prefix('ionicon'),
                                        Toggle::make('filled')
                                            ->label(__('Filled Style'))
                                            ->helperText(__('Filled or clean button style'))
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                                Section::make(__('Settings'))
                                    ->collapsible()
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Toggle::make('status')
                                                    ->default(true)
                                                    ->label(__('Active'))
                                                    ->helperText(__('Show this CTA'))
                                                    ->inline(),
                                                Toggle::make('axis')
                                                    ->default(true)
                                                    ->label(__('Invert'))
                                                    ->helperText(__('Invert the position of the CTA components'))
                                                    ->inline(true),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
