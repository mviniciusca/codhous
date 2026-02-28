<?php

namespace App\Filament\Resources;

use App\Models\Mail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Services\SendMailService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\MailResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function getNavigationBadge(): ?string
    {
        return self::count();
    }

    protected static ?string $navigationGroup = 'Communication';

    public static function count(): ?string
    {
        $count = Mail::query()
            ->where('is_read', 0)
            ->where('is_sent', 0)
            ->where('is_spam', 0)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationLabel(): string
    {
        return __('Inbox');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('Inbox'))
            ->headerActions([
                Action::make('compose_mail')
                    ->label(__('New Mail'))
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->modal(true)
                    ->closeModalByClickingAway(false)
                    ->form([
                        Hidden::make('is_sent')
                            ->default(true),
                        TextInput::make('name')
                            ->label(__('From:'))
                            ->required()
                            ->placeholder(__('Your name or Company name'))
                            ->maxLength(255)
                            ->default(env('MAIL_FROM_NAME') ?? 'Codhous Software'),
                        TextInput::make('sending_mail')
                            ->label(__('Sending E-mail:'))
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->default(env('MAIL_FROM_ADDRESS') ?? 'Codhous Software'),
                        TextInput::make('email')
                            ->label('To:')
                            ->email()
                            ->maxLength(200)
                            ->required()
                            ->placeholder(__('Email address')),
                        TextInput::make('subject')
                            ->label('Subject: ')
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('Subject of your email')),
                        RichEditor::make('message')
                            ->label('Message:')
                            ->required()
                            ->maxLength(5000)
                            ->helperText(__('Your Message. Max.: 5000 characters')),
                    ])
                    ->action(function (?array $data): void {
                        $mail = new SendMailService($data);
                        $mail->send();
                    }),
            ])
            ->description(__('Your messages from website.'))
            ->columns([
                Split::make([
                    TextColumn::make('name')
                        ->weight('bold')
                        ->searchable()
                        ->formatStateUsing(fn($state) => self::normalizeText($state))
                        ->description(fn(Mail $record) => $record->email)
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('subject')
                            ->weight('semibold')
                            ->searchable()
                            ->limit(100),
                        TextColumn::make('message')
                            ->limit(150)
                            ->color('gray')
                            ->wrap()
                            ->html(),
                    ]),
                    TextColumn::make('created_at')
                        ->dateTime('d/m/y H:i')
                        ->color('gray')
                        ->alignEnd()
                        ->description(fn(Mail $record) => $record->created_at->diffForHumans()),
                ])->extraAttributes([
                    'class' => 'py-3',
                ]),
                IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('')
                    ->falseIcon('heroicon-m-sparkles')
                    ->falseColor('primary')
                    ->alignCenter()
                    ->grow(false),
            ])
            ->recordAction('view')
            ->recordClasses(fn(Mail $record) => match ($record->is_read) {
                0, false => ['bg-primary-50/50 dark:bg-primary-500/5 border-l-4 border-l-primary-500'],
                default => ['opacity-70'],
            })
            ->paginated(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_sent')
                    ->label(__('Messages'))
                    ->indicator(__('Messages'))
                    ->trueLabel(__('Sent'))
                    ->falseLabel(__('Received'))
                    ->default(false),
                TernaryFilter::make('is_favorite')
                    ->label(__('Important'))
                    ->indicator(__('Important'))
                    ->trueLabel(__('Important'))
                    ->falseLabel(__('Not Important')),
                TernaryFilter::make('is_read')
                    ->label(__('Status'))
                    ->indicator(__('Status'))
                    ->trueLabel(__('Read Messages'))
                    ->falseLabel(__('Not Read')),
                TernaryFilter::make('is_spam')
                    ->label(__('Spam'))
                    ->indicator(__('Spam'))
                    ->trueLabel(__('Marked as Spam'))
                    ->falseLabel(__('Not Spam'))
                    ->default(false),
                TrashedFilter::make()
                    ->indicator(__('Trashed'))
                    ->label(__('Trashed')),
            ], FiltersLayout::BelowContent)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label(__('Reply'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->modalHeading(__('Reply to Message'))
                    ->hidden(fn(Mail $record) => $record->is_sent)
                    ->form([
                        TextInput::make('email')
                            ->label(__('To:'))
                            ->default(fn($record) => $record->email)
                            ->readOnly(),
                        TextInput::make('subject')
                            ->label(__('Subject:'))
                            ->default(fn($record) => "Re: " . $record->subject)
                            ->required(),
                        RichEditor::make('message')
                            ->label(__('Message:'))
                            ->required(),
                    ])
                    ->action(function (Mail $record, array $data) {
                        $data['name'] = env('MAIL_FROM_NAME') ?? 'Codhous Software';
                        $service = new \App\Services\SendMailService($data);
                        $service->send();
                    }),
                Tables\Actions\Action::make('mark_as_read')
                    ->label(__('Mark Read'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn(Mail $record) => $record->is_read)
                    ->action(fn(Mail $record) => $record->update(['is_read' => true])),
                Tables\Actions\Action::make('mark_as_unread')
                    ->label(__('Mark Unread'))
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->visible(fn(Mail $record) => $record->is_read)
                    ->action(fn(Mail $record) => $record->update(['is_read' => false])),
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Trash')),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->label(__('Mark as Read'))
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['is_read' => true])),
                    Tables\Actions\BulkAction::make('mark_as_unread')
                        ->label(__('Mark as Unread'))
                        ->icon('heroicon-o-envelope')
                        ->action(fn($records) => $records->each->update(['is_read' => false])),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Trash all Messages')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListMails::route('/'),
            'view'    => Pages\ViewMail::route('/{record}/view'),
            'create'  => Pages\CreateMail::route('/create'),
        ];
    }

    public static function normalizeText($string): string
    {
        $words = explode(' ', $string);
        $words = array_map('ucfirst', $words);

        return implode(' ', $words);
    }
}
