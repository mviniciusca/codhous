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
                            FileUpload::make('image')
                                ->label(__('Upload Image'))
                                ->image()
                                ->directory('cta')
                                ->required()
                                ->imageEditor()
                                ->imageCropAspectRatio('16:9')
                                ->helperText(__('This will automatically crop into a 16:9 ratio format'))
                                ->columnSpan(2),
                            Group::make()->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->label(__('Title'))
                                    ->columnSpan(4)
                                    ->helperText(__('Title for Content. Max 140 characters'))
                                    ->maxLength(140),
                                Textarea::make('subtitle')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpan(4)
                                    ->label(__('Subtitle'))
                                    ->helperText(__('Short description about the content. Max 240 characters'))
                                    ->maxLength(240),
                            ])->columnSpan(4),
                            FieldSet::make('Button Settings')
                                ->schema([
                                    TextInput::make('btn_text')
                                        ->label(__('Button Text (Optional)'))
                                        ->columnSpan(3)
                                        ->helperText(__('This show the button on page. Max 140 characters'))
                                        ->maxLength(140),
                                    TextInput::make('btn_url')
                                        ->label(__('Button URL (Optional)'))
                                        ->columnSpan(3)
                                        ->helperText(__('Link for the button. Max 255 characters'))
                                        ->prefixIcon('heroicon-o-link')
                                        ->maxLength(250),
                                    Toggle::make('target')
                                        ->label(__('Ext. Link'))
                                        ->helperText(__('Open this link in a new window'))
                                        ->columnSpan(1)
                                        ->inline(false)
                                        ->default(false),
                                    Toggle::make('iconLeft')
                                        ->label(__('Icon Position'))
                                        ->helperText(__('Place the icon before or after the text. Active is on the left.'))
                                        ->columnSpan(1)
                                        ->inline(false)
                                        ->default(true),
                                    TextInput::make('icon')
                                        ->label(__('Ionicon name'))
                                        ->helperText(__('Use a icon name from Ionicon. Ex.: bulb-outline'))
                                        ->columnSpan(3)
                                        ->prefix('ionicon'),
                                    Toggle::make('filled')
                                        ->label(__('Filled Style'))
                                        ->helperText(__('Filled or clean button style'))
                                        ->columnSpan(1)
                                        ->default(true)
                                        ->inline(false),
                                ])->columns(6)
                        ])->columns(6)
                    ])

            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
