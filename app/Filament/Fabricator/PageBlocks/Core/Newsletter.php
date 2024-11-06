<?php

namespace App\Filament\Fabricator\PageBlocks\Core;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Newsletter extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('core.newsletter')
            ->icon('heroicon-o-envelope')
            ->label(__('Newsletter'))
            ->schema([
                Section::make(_('Core Feature: Newsletter Module'))
                    ->icon('heroicon-o-envelope')
                    ->collapsed()
                    ->description(__('Add a E-mail catcher in your application'))
                    ->schema([
                        Toggle::make('status')
                            ->inline(true)
                            ->label(__('Show this Module'))
                            ->default(true),
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('Title (Optional)'))
                                    ->helperText(__('Title of header content. Max.: 100 characters'))
                                    ->maxLength(100),
                                TextInput::make('subtitle')
                                    ->label(__('Subtitle (Optional)'))
                                    ->helperText(__('of header content. Max.: 100 characters.'))
                                    ->maxLength(100),
                                TextInput::make('info')
                                    ->label(__('Info (Optional)'))
                                    ->helperText(__('Info of header content. Max.: 100 characters'))
                                    ->maxLength(100),
                                TextInput::make('btn_text')
                                    ->label(__('Button Text (Optional)'))
                                    ->helperText(__('Button Text. Max.: 100 characters'))
                                    ->maxLength(100),
                            ]),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
