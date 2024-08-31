<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Bumper extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.bumper')
            ->icon('heroicon-o-cube')
            ->label(__('Bumper'))
            ->schema([
                Section::make('Bumper Component')
                    ->icon('heroicon-o-bell-alert')
                    ->description(__('Place an information with a bumper card anywhere in your applications'))
                    ->collapsed()
                    ->columns(6)
                    ->schema([
                        Toggle::make('status')
                            ->label('Active')
                            ->helperText(__('Visibility'))
                            ->inline(false)
                            ->default(true),
                        TextInput::make('info')
                            ->helperText(__('Tag before title'))
                            ->label('Tag')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(50),
                        TextInput::make('title')
                            ->label('Title')
                            ->helperText(__('Set the title of the bumper'))
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(120),
                        TextInput::make('link')
                            ->label('Link')
                            ->helperText(__('URL Link (same window)'))
                            ->required()
                            ->columnSpan(3)
                            ->prefixIcon('heroicon-o-link')
                            ->maxLength(120),
                        Group::make()
                            ->columnSpan(3)
                            ->schema([
                                Select::make('bumper_position')
                                    ->helperText(__('Bumper position. Default value is center'))
                                    ->label('Bumper Position')
                                    ->options([
                                        'center' => 'Center',
                                        'left' => 'Left',
                                        'right' => 'Right',
                                    ])
                                    ->default('center')
                                    ->selectablePlaceholder(false)
                            ])
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
