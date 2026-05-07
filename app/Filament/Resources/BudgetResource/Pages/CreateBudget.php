<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    public function getTitle(): string 
    {
        return 'Criar Novo Orçamento';
    }

    public function mount(): void
    {
        parent::mount();

        $payload = request()->query('payload');

        if ($payload && request()->query('source') === 'calculator') {
            try {
                $data = json_decode(base64_decode($payload), true);
                
                if ($data && isset($data['items'])) {
                    $items = [];
                    foreach ($data['items'] as $item) {
                        $items[] = [
                            'product_id' => $item['product_id'],
                            'product_option_id' => $item['product_option_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ];
                    }
                    
                    // We need to preserve existing content structure if any, 
                    // but usually on create it's empty.
                    $subtotal = 0;
                    foreach ($items as $item) {
                        $subtotal += ($item['quantity'] * $item['price']);
                    }
                    $tax = floatval($data['tax'] ?? 0);
                    $discount = floatval($data['discount'] ?? 0);
                    $total = $subtotal + $tax - $discount;

                    $this->form->fill([
                        'is_active' => true,
                        'status' => 'pending',
                        'created_at' => now(),
                        'budgetItems' => $items,
                        'content' => [
                            'tax' => $tax,
                            'discount' => $discount,
                            'subtotal' => number_format($subtotal, 2, '.', ''),
                            'total' => number_format($total, 2, '.', ''),
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail if payload is invalid
            }
        }
    }
}
