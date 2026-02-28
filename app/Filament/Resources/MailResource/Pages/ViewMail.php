<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Actions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split as InfolistSplit;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMail extends ViewRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label(__('Reply'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->modalHeading(__('Reply to Message'))
                ->modalSubmitActionLabel(__('Send Message'))
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
            Actions\DeleteAction::make()
                ->label('Move')
                ->icon('heroicon-o-trash'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record->is_read) {
            $this->record->update([
                'is_read' => 1,
            ]);
        }
    }


    public function getTitle(): Htmlable|string
    {
        return __('Mail');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Message'))
                    ->icon('heroicon-o-envelope')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(new \Illuminate\Support\HtmlString(__('<strong>From:</strong>'))),
                        TextEntry::make('email')
                            ->label(new \Illuminate\Support\HtmlString(__('<strong>Email:</strong>'))),
                        TextEntry::make('created_at')
                            ->dateTime('d/m/y H:i')
                            ->label(new \Illuminate\Support\HtmlString(__('<strong>Received At</strong>'))),
                        TextEntry::make('subject')
                            ->columnSpanFull()
                            ->label(new \Illuminate\Support\HtmlString(__('<strong>Subject:</strong>'))),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->html()
                            ->label(new \Illuminate\Support\HtmlString(__('<strong>Message:</strong>'))),
                    ])
                    ->footerActions([
                        InfolistAction::make('reply_infolist')
                            ->label(__('Reply to Message'))
                            ->icon('heroicon-o-arrow-uturn-left')
                            ->color('primary')
                            ->button()
                            ->modalHeading(__('Reply to Message'))
                            ->modalSubmitActionLabel(__('Send Message'))
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
                            })
                    ]),
            ]);
    }
}
