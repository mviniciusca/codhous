<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Models\Mail;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\MailResource;
use Filament\Resources\Pages\ListRecords;

class FavoriteMail extends ListRecords
{
    protected static string $resource = MailResource::class;
    public function table(Table $table): Table
    {
        return $table
            ->query(Mail::query()->where('is_favorite', true))
            ->columns([
                TextColumn::make('email')
            ]);
    }
}
