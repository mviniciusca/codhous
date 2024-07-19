<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class SpamMail extends ListRecords
{
    public static function count(): string
    {
        return Mail::query()
            ->where('is_spam', true)
            ->where('is_sent', false)
            ->count();
    }
    protected static string $resource = MailResource::class;
}
