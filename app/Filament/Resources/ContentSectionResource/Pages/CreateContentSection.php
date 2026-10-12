<?php

namespace App\Filament\Resources\ContentSectionResource\Pages;

use App\Filament\Resources\ContentSectionResource;
use App\Filament\Resources\ContentSectionResource\Concerns\InteractsWithSectionBuilder;
use Filament\Resources\Pages\CreateRecord;

class CreateContentSection extends CreateRecord
{
    use InteractsWithSectionBuilder;

    protected static string $resource = ContentSectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Escolha um componente, configure o conteúdo e ajuste a aparência.';
    }
}
