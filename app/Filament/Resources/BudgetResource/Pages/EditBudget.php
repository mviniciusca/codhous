<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    public function getTitle(): string 
    {
        return 'Detalhes do Orçamento';
    }

    public function getSubheading(): ?string
    {
        return 'Revise as informações do cliente, defina os preços e gere o PDF para envio.';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $fieldsToFormat = ['tax', 'discount', 'subtotal', 'shipping', 'total'];
        
        if (isset($data['content']) && is_array($data['content'])) {
            foreach ($fieldsToFormat as $field) {
                if (isset($data['content'][$field])) {
                    $val = $data['content'][$field];
                    if (is_numeric($val) && !str_contains((string)$val, ',')) {
                        $data['content'][$field] = number_format(floatval($val), 2, ',', '.');
                    }
                }
            }
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Salvar Orçamento')
                ->action('save')
                ->color('primary')
                ->keyBindings(['mod+s']),
            Actions\Action::make('cancel')
                ->label('Voltar')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
            Actions\ForceDeleteAction::make()
                ->label('Excluir Permanente'),
            Actions\RestoreAction::make()
                ->label('Restaurar'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
