<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use DateTime;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
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
            Actions\DeleteAction::make(),
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
                Section::make(__('Mail Message'))
                    ->icon('heroicon-o-envelope')
                    ->collapsible()
                    ->columns(5)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('From:'))
                            ->disabled(),
                        TextInput::make('email')
                            ->label(__('Email:'))
                            ->disabled(),
                        TextInput::make('subject')
                            ->label(__('Subject:'))
                            ->columnSpan(2)
                            ->disabled(),
                        DateTimePicker::make('created_at')
                            ->label(__('Date / Hour: '))
                            ->format('d/m/y H:i:s')
                            ->disabled(),
                        Textarea::make('message')
                            ->label(__('Message:'))
                            ->columnSpanFull()
                            ->rows(5)
                            ->disabled(),
                    ]),
                Section::make(__('Actions'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->columns(6)
                    ->schema([
                        Toggle::make('is_favorite')
                            ->inline(false)
                            ->label(__('Mark as Important')),
                        Toggle::make('is_read')
                            ->inline(false)
                            ->label(__('Mark as Read')),
                        Toggle::make('is_spam')
                            ->inline(false)
                            ->label(__('Tag as Spam')),
                    ]),
            ]);
    }

}
