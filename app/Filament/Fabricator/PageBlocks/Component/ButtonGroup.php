<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ButtonGroup extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.button-group')
            ->icon('heroicon-o-cube')
            ->label(__('Button Group'))
            ->schema([
                Section::make('Group Buttons')
                    ->description('A group with a pair of buttons')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->collapsed()
                    ->schema([
                        Group::make()
                            ->schema([
                                Fieldset::make('Background Filled Button')
                                    ->columns(6)
                                    ->schema([
                                        Toggle::make('btn_full_status')
                                            ->inline(false)
                                            ->default(true)
                                            ->label('Active'),
                                        TextInput::make('btn_full_text')
                                            ->columnSpan(3)
                                            ->label('Button Filled Text')
                                            ->maxLength(50),
                                        TextInput::make('btn_full_icon')
                                            ->columnSpan(2)
                                            ->prefixIcon('heroicon-o-light-bulb')
                                            ->label('Ionicon Name')
                                            ->helperText('Ionicon icon name'),
                                        TextInput::make('btn_full_url')
                                            ->columnSpanFull()
                                            ->label('Button URL')
                                            ->prefixIcon('heroicon-o-link')
                                    ]),
                            ]),
                        Group::make()->schema([
                            Fieldset::make('Simply Button')
                                ->columns(6)
                                ->schema([
                                    Toggle::make('btn_status')
                                        ->inline(false)
                                        ->default(true)
                                        ->label('Active'),
                                    TextInput::make('btn_text')
                                        ->columnSpan(3)
                                        ->label('Button Text')
                                        ->maxLength(50),
                                    TextInput::make('btn_icon')
                                        ->columnSpan(2)
                                        ->prefixIcon('heroicon-o-light-bulb')
                                        ->label('Ionicon Name')
                                        ->helperText('Ionicon icon name'),
                                    TextInput::make('btn_url')
                                        ->columnSpanFull()
                                        ->label('Button URL')
                                        ->prefixIcon('heroicon-o-link')
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
