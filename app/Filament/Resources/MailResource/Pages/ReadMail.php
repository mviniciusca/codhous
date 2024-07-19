<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ReadMail extends ListRecords
{
    protected static string $resource = MailResource::class;

    public static function count(): string
    {
        return Mail::query()
            ->where('is_read', true)
            ->where('is_spam', false)
            ->count();
    }
}
