<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Models\Mail;
use App\Models\User;
use App\Mail\Message;
use Filament\Actions;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Filament\Resources\MailResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail as Mailer;

class ListMails extends ListRecords
{
    protected static string $resource = MailResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Inbox');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_mail')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->label(__('New Mail'))
                ->closeModalByClickingAway(false)
                ->form([
                    Hidden::make('is_sent')
                        ->default(true),
                    Hidden::make('name')
                        ->default(env('APP_NAME')),
                    TextInput::make('email')
                        ->label('To: ')
                        ->required()
                        ->placeholder(__('Email address')),
                    TextInput::make('phone')
                        ->label('To: ')
                        ->required()
                        ->placeholder(__('Phone Number')),
                    TextInput::make('subject')
                        ->label('Subject: ')
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Subject of your email')),
                    RichEditor::make('message')
                        ->label('Message: ')
                        ->required()
                        ->maxLength(5000)
                        ->helperText(__('Your Message. Max.: 5000 characters')),
                ])
                ->action(function (Mail $mail, ?array $data): void {

                    // send the email to destiny
                    Mailer::to($data['email'])->send(new Message($data));


                    // save the message in database                
                    $mail->create($data);


                    // show a notification 
                    Notification::make()
                        ->success()
                        ->title(__('Message was sent with success'))
                        ->send();
                })
        ];
    }
}
