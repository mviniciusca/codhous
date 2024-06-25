<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ImportantMail extends ListRecords
{
    protected static string $resource = MailResource::class;

    protected static ?string $navigationParentItem = 'Mail';

}
