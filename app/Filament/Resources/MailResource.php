<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailResource\Pages;
use App\Mail\Message;
use App\Models\Mail;
use App\Services\SendMail;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail as MailFacade;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function getNavigationBadge(): ?string
    {
        return self::count();
    }

    protected static ?string $navigationGroup = 'Mail';

    public static function count(): ?string
    {
        $count = Mail::query()
            ->where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->count();

        return $count !== 0 ? $count : null;
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
                        Group::make()
                            ->columns(2)
                            ->schema([
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
                            ]),
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
                        $mail = new SendMail($data);
                        $mail->send();
                    }),
            ])
            ->description(__('Your messages from website.'))
            ->columns([
                IconColumn::make('is_favorite')
                    ->label(__(''))
                    ->inline()
                    ->trueIcon('heroicon-m-bookmark')
                    ->trueColor('primary')
                    ->falseIcon('')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->limit(50)
                    ->formatStateUsing(function (?string $state) {
                        return self::normalizeText($state);
                    })
                    ->label(__('From')),
                TextColumn::make('subject')
                    ->limit(60)
                    ->formatStateUsing(function (?string $state) {
                        return ucfirst($state);
                    })
                    ->label(ucfirst(__('Subject'))),
                TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->date('d/m/Y H:i'),
            ])
            ->searchable()
            ->recordClasses(fn (Mail $record) => match ($record->is_read) {
                1       => 'opacity-30',
                default => null,
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
                    ->default(false)
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
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Trash')),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'starred' => Pages\StarredMail::route('/starred'),
            'sent'    => Pages\SentMail::route('/sent'),
            'view'    => Pages\ViewMail::route('/{record}/view'),
            'create'  => Pages\CreateMail::route('/create'),
            'fav'     => Pages\FavoriteMail::route('/fav'),
            'read'    => Pages\ReadMail::route('/read'),
            'spam'    => Pages\SpamMail::route('/spam'),
            'bin'     => Pages\BinMail::route('/bin'),
        ];
    }

    public static function normalizeText($string): string
    {
        $words = explode(' ', $string);
        $words = array_map('ucfirst', $words);

        return implode(' ', $words);
    }
}
