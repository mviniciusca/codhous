<?php

namespace App\Filament\Fabricator\PageBlocks;

use Faker\Core\File;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class HeaderNav extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('header-nav')
            ->schema([
                Section::make('Header Menu')
                    ->description('Settings of the Menu')
                    ->icon('heroicon-o-cube')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Group::make()->schema([
                            FileUpload::make('logo')
                                ->image()
                                ->imageEditor()
                        ]),
                        Group::make()->schema([
                            TextInput::make('menu_btn')
                                ->label('Button Text')
                                ->maxLength(50),
                            TextInput::make('menu_btn_link')
                                ->prefixIcon('heroicon-o-link')
                                ->label('Button Link')
                        ]),
                        Section::make('Navigation Menu')
                            ->description('Navigation itens control')
                            ->icon('heroicon-o-link')
                            ->collapsed()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Repeater::make('navigations')
                                            ->schema([
                                                TextInput::make('navlink_text')
                                                    ->label('Menu Text'),
                                                TextInput::make('navlink_url')
                                                    ->label('Link URL')
                                                    ->url()
                                                    ->prefixIcon('heroicon-o-link'),
                                            ])->columns(2),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
