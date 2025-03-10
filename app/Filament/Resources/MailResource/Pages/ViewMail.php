<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewMail extends ViewRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_favorite')
                ->icon('heroicon-o-star')
                ->color(function () {
                    return $this->toggle('is_favorite');
                })
                ->disabled()
                ->label(__('Fav')),
            Action::make('mark_spam')
                ->icon('heroicon-o-speaker-x-mark')
                ->color(function () {
                    return $this->toggle('is_spam');
                })
                ->disabled()
                ->label(__('Spam')),
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

    public function toggle(?string $column)
    {
        $check = Mail::select($column)
            ->where('id', '=', request()->route('record'))
            ->first([$column]);

        if ($check) {
            if ($check[$column] == true) {
                return match ($column) {
                    'is_spam' => 'warning',
                    default   => 'primary',
                };
            } else {
                return 'secondary';
            }
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
                            ->label(new HtmlString(__('<strong>From:</strong>'))),
                        TextEntry::make('email')
                            ->label(new HtmlString(__('<strong>Email:</strong>'))),
                        TextEntry::make('created_at')
                            ->dateTime('d/m/y H:i')
                            ->label(new HtmlString(__('<strong>Received At</strong>'))),
                        TextEntry::make('subject')
                            ->columnSpanFull()
                            ->label(new HtmlString(__('<strong>Subject:</strong>'))),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->label(new HtmlString(__('<strong>Message:</strong>'))),
                    ]),
            ]);
    }
}
