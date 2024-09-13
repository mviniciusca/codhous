<?php

namespace App\Filament\Widgets;

use App\Models\Mail;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MailWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    //protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->description(__('Quick view in your unread messages here.'))
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
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->limit(30),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(30),
            ])
            ->heading(__('Inbox (' .
                Mail::where('is_read', false)
                    ->where('is_sent', false)
                    ->where('is_spam', false)
                    ->withoutTrashed()
                    ->count()) . ')')
            ->striped()
            ->paginated(false);
    }
}
