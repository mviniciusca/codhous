<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use Filament\Actions;
use App\Models\Newsletter;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\NewsletterResource;
use Z3d0X\FilamentFabricator\Resources\PageResource\Pages\ViewPage;

class SubscriberBin extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    public static function count(): ?string
    {
        $count = Newsletter::onlyTrashed()->count();
        return $count !== 0 ? $count : null;
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(Newsletter::onlyTrashed())
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('Status')),
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('email')
                    ->label(__('Email')),
            ]);
    }
}
