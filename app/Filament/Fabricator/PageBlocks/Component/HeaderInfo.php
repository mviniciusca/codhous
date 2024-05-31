<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class HeaderInfo extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.header-info')
            ->icon('heroicon-o-cube')
            ->label(__('Section Title + Subtitle'))
            ->schema([
                Section::make(__('Section Title + Subtitle'))
                    ->description(__('Header and subtitle box info'))
                    ->icon('heroicon-o-pencil')
                    ->columns(6)
                    ->collapsed()
                    ->schema([
                        Toggle::make('status')
                            ->inline(false)
                            ->label(__('Active'))
                            ->columnSpan(1)
                            ->default(true),
                        TextInput::make('title')
                            ->columnSpan(5)
                            ->label(__('Title'))
                            ->helperText(__('Add a title for the section'))
                            ->maxLength(255)
                            ->required(),
                        Group::make()
                            ->columnSpanFull()
                            ->schema([
                                Textarea::make('subtitle')
                                    ->label(__('Subtitle'))
                                    ->helperText(__('Add a subtitle for the section'))
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->required(),
                            ]),
                        Section::make('Layout')
                            ->description(__('Layout Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(2)
                            ->collapsed()
                            ->schema([
                                Select::make('title_font_size')
                                    ->options([
                                        'small' => __('Small'),
                                        'normal' => __('Normal'),
                                        'large' => __('Large')
                                    ])
                                    ->default('normal'),
                                Select::make('position')
                                    ->options([
                                        'center' => __('Center'),
                                        'left' => __('Left'),
                                    ])
                                    ->default('center')
                                    ->selectablePlaceholder(true)
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
