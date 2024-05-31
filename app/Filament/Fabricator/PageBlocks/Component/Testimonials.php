<?php

namespace App\Filament\Fabricator\PageBlocks\Component;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Testimonials extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('component.testimonials')
            ->icon('heroicon-o-cube')
            ->label(__('Testimonials'))
            ->schema([
                Section::make('Testimonials')
                    ->icon('heroicon-o-user')
                    ->description('Add a Testimonial to your application')
                    ->collapsible()
                    ->columns(6)
                    ->schema([
                        FileUpload::make('avatar')
                            ->directory('/testimonial')
                            ->label('Avatar')
                            ->image()
                            ->imageEditor(),
                        Group::make()
                            ->columnSpan(5)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name'),
                                TextInput::make('job_position')
                                    ->label('Job Position'),
                                Textarea::make('opinion')
                                    ->columnSpanFull()
                                    ->label('Opinion'),
                            ]),
                    ])

            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
