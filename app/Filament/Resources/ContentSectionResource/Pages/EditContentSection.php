<?php

namespace App\Filament\Resources\ContentSectionResource\Pages;

use App\Filament\Resources\ContentSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentSection extends EditRecord
{
    protected static string $resource = ContentSectionResource::class;

    public function getTitle(): string
    {
        return "Editar Seção: " . $this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
