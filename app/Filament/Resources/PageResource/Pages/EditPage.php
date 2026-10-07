<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function getTitle(): string
    {
        return "Editar Página: " . $this->getRecord()->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('visit')
                ->label('Visitar Página')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn ($record) => url($record->slug === '/' || $record->slug === 'home' || $record->slug === 'index' ? '/' : '/' . ltrim($record->slug, '/')))
                ->openUrlInNewTab(),
            Actions\Action::make('preview')
                ->label('Preview Interno')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->modalHeading(fn ($record) => 'Preview: ' . $record->title)
                ->modalContent(fn ($record) => view('filament.pages.preview-modal', ['record' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalWidth(\Filament\Support\Enums\MaxWidth::Screen),
            Actions\Action::make('save')
                ->label('Salvar')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->icon('heroicon-o-trash'),
        ];
    }
}
