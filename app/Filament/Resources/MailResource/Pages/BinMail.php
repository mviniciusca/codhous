<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Models\Mail;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\MailResource;
use Filament\Resources\Pages\ListRecords;

class BinMail extends ListRecords
{
    protected static string $resource = MailResource::class;

    public static function count(): string
    {
        return Mail::onlyTrashed()
            ->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Mail::onlyTrashed())
            ->columns([
                TextColumn::make('email')
            ]);
    }
}
