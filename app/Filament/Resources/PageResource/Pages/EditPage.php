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
            Actions\Action::make('view')
                ->label('Ver página')
                ->url(fn ($record) => url($record->slug === 'home' || $record->slug === 'index' ? '/' : '/' . $record->slug))
                ->openUrlInNewTab()
                ->icon('heroicon-o-eye')
                ->color('gray'),
            Actions\Action::make('save')
                ->label('Salvar')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
            Actions\DeleteAction::make(),
        ];
    }
}
