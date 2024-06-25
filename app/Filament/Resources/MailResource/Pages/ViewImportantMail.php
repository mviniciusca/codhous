<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ViewImportantMail extends ListRecords
{
    protected static ?string $navigationGroup = 'Mail';
    protected static ?string $navigationParentItem = 'Mail';
    protected static string $resource = MailResource::class;
}
