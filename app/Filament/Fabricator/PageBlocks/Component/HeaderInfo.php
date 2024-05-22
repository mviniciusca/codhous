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
            ->schema([

                Section::make('Header Info')
                    ->description('Header and subtitle box info')
                    ->icon('heroicon-o-cube')
                    ->columns(6)
                    ->collapsed()
                    ->schema([
                        Toggle::make('status')
                            ->inline(false)
                            ->label('Active')
                            ->columnSpan(1)
                            ->default(true),
                        TextInput::make('title')
                            ->columnSpan(5)
                            ->label('Title')
                            ->required(),
                        Group::make()
                            ->columnSpanFull()
                            ->schema([
                                Textarea::make('subtitle')
                                    ->label('Sub-title')
                                    ->columnSpanFull()
                                    ->rows(4)
                                    ->required(),
                            ]),
                        Section::make('Layout')
                            ->description('Layout Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(2)
                            ->collapsed()
                            ->schema([
                                Select::make('title_font_size')
                                    ->options([
                                        'small' => 'Small',
                                        'normal' => 'Normal',
                                        'large' => 'Large'
                                    ])
                                    ->default('normal'),
                                Select::make('position')
                                    ->options([
                                        'center' => 'Center',
                                        'left' => 'Left',
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
