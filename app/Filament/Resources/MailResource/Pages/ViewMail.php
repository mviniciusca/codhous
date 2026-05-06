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
                ->label('Responder')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->modalHeading('Responder Mensagem')
                ->modalSubmitActionLabel('Enviar Resposta')
                ->hidden(fn(Mail $record) => $record->is_sent)
                ->form([
                    TextInput::make('email')
                        ->label('Para:')
                        ->helperText('Destinatário da resposta.')
                        ->default(fn($record) => $record->email)
                        ->readOnly(),
                    TextInput::make('subject')
                        ->label('Assunto:')
                        ->helperText('Título da resposta.')
                        ->default(fn($record) => "Re: " . $record->subject)
                        ->required(),
                    RichEditor::make('message')
                        ->label('Mensagem:')
                        ->helperText('Escreva sua resposta abaixo.')
                        ->required(),
                ])
                ->action(function (Mail $record, array $data) {
                    $data['name'] = env('MAIL_FROM_NAME') ?? 'Codhous Software';
                    $service = new \App\Services\SendMailService($data);
                    $service->send();
                }),
            Actions\DeleteAction::make()
                ->label('Mover para Lixeira')
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
        return 'Visualizar Mensagem';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalhes da Mensagem')
                    ->icon('heroicon-o-envelope')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(new \Illuminate\Support\HtmlString('<strong>De:</strong>')),
                        TextEntry::make('email')
                            ->label(new \Illuminate\Support\HtmlString('<strong>E-mail:</strong>')),
                        TextEntry::make('created_at')
                            ->dateTime('d/m/y H:i')
                            ->label(new \Illuminate\Support\HtmlString('<strong>Recebido em:</strong>')),
                        TextEntry::make('subject')
                            ->columnSpanFull()
                            ->label(new \Illuminate\Support\HtmlString('<strong>Assunto:</strong>')),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->html()
                            ->label(new \Illuminate\Support\HtmlString('<strong>Mensagem:</strong>')),
                    ])
                    ->footerActions([
                        InfolistAction::make('reply_infolist')
                            ->label('Responder Mensagem')
                            ->icon('heroicon-o-arrow-uturn-left')
                            ->color('primary')
                            ->button()
                            ->modalHeading('Responder Mensagem')
                            ->modalSubmitActionLabel('Enviar Resposta')
                            ->hidden(fn(Mail $record) => $record->is_sent)
                            ->form([
                                TextInput::make('email')
                                    ->label('Para:')
                                    ->helperText('Destinatário da resposta.')
                                    ->default(fn($record) => $record->email)
                                    ->readOnly(),
                                TextInput::make('subject')
                                    ->label('Assunto:')
                                    ->helperText('Título da resposta.')
                                    ->default(fn($record) => "Re: " . $record->subject)
                                    ->required(),
                                RichEditor::make('message')
                                    ->label('Mensagem:')
                                    ->helperText('Escreva sua resposta abaixo.')
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
