<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use Filament\Actions;
use App\Models\Newsletter;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\NewsletterResource;

class SubscriberBin extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(Newsletter::onlyTrashed())
            ->columns([
                ToggleColumn::make('is_active'),
                TextColumn::make('name'),
                TextColumn::make('email'),
            ]);
    }
}
