<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Task extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static string $view = 'filament.pages.task';

    protected static ?string $title = 'Tasks';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $slug = 'tasks';
}
