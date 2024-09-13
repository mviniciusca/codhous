<?php

namespace App\Filament\Widgets;

use App\Models\Mail;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Widgets\TableWidget as BaseWidget;

class MailWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    public Mail $mail;
    protected static ?string $model = Mail::class;
    //protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn(Mail $record): string => route('filament.admin.resources.mails.view', ['record' => $record]),
            )
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
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(30),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('Quick View'))
                        ->form([
                            TextInput::make('name'),
                            TextInput::make('email'),
                            TextInput::make('subject'),
                            Textarea::make('message'),
                        ]),
                    DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                    ->model(Mail::class),
            ])
            ->headerActions([
                Action::make('edit')
                    ->label(__('Inbox'))
                    ->icon('heroicon-o-envelope')
                    ->url(route('filament.admin.resources.mails.index'))
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
