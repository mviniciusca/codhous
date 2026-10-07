<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditPage extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = PageResource::class;

    protected function getPreviewModalUrl(): ?string
    {
        $record = $this->getRecord();
        return url($record->slug === '/' || $record->slug === 'home' || $record->slug === 'index' ? '/' : '/' . ltrim($record->slug, '/'));
    }

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
            PreviewAction::make()
                ->label('Preview Interno')
                ->color('gray'),
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
