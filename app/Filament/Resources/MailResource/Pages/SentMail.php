<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class SentMail extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = MailResource::class;
    protected static string $view = 'filament.resources.mail-resource.pages.sent-mail';

    public function table(Table $table): Table
    {
        return $table
            ->query(Mail::query()->where('is_sent', true))
            ->striped()
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
            ]);
    }

}
