<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Fieldset;
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
                Fieldset::make('module_settings')
                    ->columns(2)
                    ->label(__('Module Settings'))
                    ->schema([
                        Toggle::make('status')
                            ->default(true)
                            ->label(__('Active Module'))
                            ->helperText(__('Active or disable this section')),
                        Toggle::make('section_filled')
                            ->default(true)
                            ->label(__('Fill Section'))
                            ->helperText(__('Fill this section with a contrast color')),
                    ]),
                Section::make(__('Section Title + Subtitle'))
                    ->description(__('Header and subtitle box info'))
                    ->icon('heroicon-o-pencil')
                    ->columns(4)
                    ->collapsed()
                    ->schema([
                        TextInput::make('title')
                            ->columnSpanFull()
                            ->label(__('Title'))
                            ->helperText(__('Section title. HTML allowed ✨'))
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
                                    ->rows(3),
                            ]),

                        Toggle::make('padding')
                            ->label(__('Vertical Padding'))
                            ->inline(false)
                            ->helperText(__('Applies a vertical space in the title'))
                            ->default(false),
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
                            ->selectablePlaceholder(true),
                        Select::make('color')
                            ->label(__('Text Color'))
                            ->helperText(__('Set the color of the text. This affects the title and subtitle'))
                            ->options([
                                'dark' => __('Dark'),
                                'light' => __('Light'),
                                'default' => __('Default'),
                            ])->default('default'),
                    ]),

            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
