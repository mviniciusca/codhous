<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Filament\Resources\MailResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

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
                ->form([
                    Hidden::make('is_sent')
                        ->default(true),
                    TextInput::make('email')
                        ->label('Mail To: ')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Email address')),
                    TextInput::make('subject')
                        ->label('Subject: ')
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Subject')),
                    RichEditor::make('message')
                        ->label('Message: ')
                        ->required()
                        ->maxLength(5000)
                        ->placeholder(__('Message. Max.: 5000 characters')),
                ])
        ];
    }
}
