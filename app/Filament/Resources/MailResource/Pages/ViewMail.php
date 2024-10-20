<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMail extends EditRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('Trash'))
                ->icon('heroicon-o-trash'),
        ];
    }
    public function getTitle(): Htmlable|string
    {
        return __('Mail');
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Section::make(__('Actions'))
                    ->description(__('Actions for this message'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_favorite')
                            ->inline(true)
                            ->label(__('Mark as Important')),
                        Toggle::make('is_read')
                            ->inline(true)
                            ->label(__('Mark as Read')),
                        Toggle::make('is_spam')
                            ->inline(true)
                            ->label(__('Tag as Spam')),
                    ]),
                Section::make(__('Mail Message'))
                    ->description(__('Your mail content'))
                    ->icon('heroicon-o-envelope')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('From:'))
                            ->disabled(),
                        TextInput::make('email')
                            ->label(__('Email:'))
                            ->disabled(),
                        DateTimePicker::make('created_at')
                            ->label(__('Date / Hour: '))
                            ->format('d/m/y H:i:s')
                            ->disabled(),
                        TextInput::make('subject')
                            ->label(__('Subject:'))
                            ->columnSpanFull()
                            ->disabled(),
                        Textarea::make('message')
                            ->label(__('Message:'))
                            ->columnSpanFull()
                            ->rows(5)
                            ->disabled(),
                    ]),
            ]);
    }

}
