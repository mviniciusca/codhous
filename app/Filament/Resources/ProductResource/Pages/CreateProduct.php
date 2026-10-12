<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Novo produto ou serviço';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Entra no fim da lista do site
        $data['sort'] = (int) Product::withTrashed()->max('sort') + 1;

        return $data;
    }
}
