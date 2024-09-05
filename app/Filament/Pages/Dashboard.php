<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Page;

class Dashboard extends PagesDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';
}
