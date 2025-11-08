<?php

namespace App\Filament\Widgets;

use App\Models\Mail;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class MailWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    public Mail $mail;

    protected static ?string $model = Mail::class;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $unreadCount = Mail::where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->withoutTrashed()
            ->count();

        return $table
            ->recordUrl(
                fn (Mail $record): string => route('filament.admin.resources.mails.view', ['record' => $record]),
            )
            ->description(__('Most recent unread messages in your inbox'))
            ->query(
                Mail::query()
                    ->where('is_spam', false)
                    ->where('is_read', false)
                    ->where('is_sent', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->emptyStateHeading(__('No unread messages'))
            ->emptyStateDescription(__('All messages have been read or marked as read.'))
            ->emptyStateIcon('heroicon-o-envelope-open')
            ->columns([
                IconColumn::make('priority')
                    ->boolean()
                    ->label(__('Priority'))
                    ->getStateUsing(fn (Mail $record): bool => Str::contains(strtolower($record->subject), ['urgent', 'important', 'priority']) ||
                        $record->created_at > now()->subHours(24)
                    )
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-envelope')
                    ->falseColor('gray'),

                TextColumn::make('name')
                    ->label(__('From'))
                    ->searchable()
                    ->limit(20)
                    ->tooltip(fn (Mail $record): string => "{$record->name} <{$record->email}>"),

                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (Mail $record): ?string => strlen($record->subject) > 40 ? $record->subject : null
                    ),

                TextColumn::make('message')
                    ->label(__('Preview'))
                    ->limit(60)
                    ->tooltip(fn (Mail $record): ?string => strlen($record->message) > 60 ? Str::limit($record->message, 200) : null
                    ),

                TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        if ($date->isToday()) {
                            return __('Today').' '.$date->format('H:i');
                        } elseif ($date->isYesterday()) {
                            return __('Yesterday').' '.$date->format('H:i');
                        } else {
                            return $date->format('d/m/Y H:i');
                        }
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('mark_as_read')
                        ->label(__('Mark as Read'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (Mail $record): void {
                            $record->update(['is_read' => true]);
                        })
                        ->successNotificationTitle(__('Message marked as read')),

                    ViewAction::make()
                        ->modalHeading(__('Quick View: Mail'))
                        ->label(__('Quick View'))
                        ->form([
                            Group::make()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->helperText(__('Sender\'s Name'))
                                        ->label(__('Name')),
                                    TextInput::make('email')
                                        ->helperText(__('Sender\'s Email address'))
                                        ->label(__('Email')),
                                    TextInput::make('subject')
                                        ->helperText(__('Message Subject'))
                                        ->label(__('Subject')),
                                    DatePicker::make('created_at')
                                        ->label(__('Received'))
                                        ->format('d/m/Y H:i'),
                                    Textarea::make('message')
                                        ->helperText(__('Sender\'s Message'))
                                        ->rows(5)
                                        ->columnSpanFull()
                                        ->label(__('Message')),
                                ]),
                        ]),

                    Action::make('reply')
                        ->label(__('Reply'))
                        ->icon('heroicon-o-paper-airplane')
                        ->url(fn (Mail $record): string => route('filament.admin.resources.mails.create', ['reply_to' => $record->id])
                        ),

                    DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                    ->model(Mail::class),
            ])
            ->headerActions([
                Action::make('view_all')
                    ->label(__('View Inbox'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->url(route('filament.admin.resources.mails.index')),
            ])
            ->heading($unreadCount === 0
                ? __('Inbox')
                : __('Inbox (:count)', ['count' => $unreadCount]))
            ->striped()
            ->paginated(false);
    }
}
