<?php

namespace App\Filament\Widgets;

use App\Models\Mail;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MailWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    //protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Mail::query()
                    ->where('is_spam', false)
                    ->where('is_read', false)
                    ->where('is_sent', false)
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->limit(30),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(30),
            ])
            ->striped()
            ->paginated(false);
    }
}
