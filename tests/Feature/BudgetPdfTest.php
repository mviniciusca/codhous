<?php

use App\Enums\ProductUnit;
use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\BudgetPdf;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $product = Product::create(['name' => 'Concreto Usinado', 'is_active' => true]);
    $option = ProductOption::create([
        'product_id' => $product->id, 'name' => 'FCK 25', 'slug' => 'fck-25',
        'unit' => ProductUnit::M3, 'price' => 400, 'is_active' => true,
    ]);

    $this->budget = Budget::create([
        'user_id' => null,
        'content' => [
            'customer_name' => 'Maria da Silva',
            'customer_email' => 'maria@example.com',
            'customer_phone' => '(21) 96645-2451',
            'postcode' => '22630-100',
            'street' => 'Rua Ulisses Alcântara',
            'number' => '122',
            'complement' => 'Lote 5',
            'neighborhood' => 'Barra da Tijuca',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'delivery_date' => '2026-12-01',
            'delivery_period' => 'manha',
            'needs_pump' => 'sim',
            'notes' => 'Laje no 2º andar',
            'shipping' => 150,
            'tax' => 10,
            'discount' => 60,
            // Valores gravados divergentes de propósito: o PDF deve seguir os itens
            'subtotal' => 999,
            'total' => 999,
        ],
    ]);

    $this->budget->budgetItems()->create([
        'product_id' => $product->id,
        'product_option_id' => $option->id,
        'quantity' => 2.5,
        'price' => 400,
        'subtotal' => 1000,
    ]);
});

function renderInvoice(Budget $budget, $company = null): string
{
    return view('pdf.invoice', [
        'state' => $budget->toArray(),
        'budget' => $budget,
        'company' => $company,
        'layout' => null,
    ])->render();
}

test('PDF calcula os totais a partir dos itens', function () {
    $html = renderInvoice($this->budget);

    expect($html)
        ->toContain('R$ 1.000,00')          // subtotal: 2,5 m³ x R$ 400
        ->toContain('R$ 1.100,00')          // total: 1000 + 150 + 10 - 60
        ->toContain('2,5')
        ->not->toContain('R$ 999,00');
});

test('PDF mostra os dados de entrega, complemento e observações do cliente', function () {
    $html = renderInvoice($this->budget);

    expect($html)
        ->toContain('Lote 5')
        ->toContain('01/12/2026')
        ->toContain('Manhã')
        ->toContain('Sim, preciso de bomba')
        ->toContain('Laje no 2º andar')
        ->toContain('maria@example.com');
});

test('PDF é gerado mesmo sem os dados da empresa configurados', function () {
    expect(renderInvoice($this->budget, null))->toContain('Maria da Silva');
    expect(renderInvoice($this->budget, (object) []))->toContain('Maria da Silva');
});

test('admin gera o PDF e o cliente baixa pelo link com token', function () {
    $pdf = BudgetResource::generatePdfModel($this->budget);

    try {
        expect($pdf)->toBeInstanceOf(BudgetPdf::class)
            ->and(file_exists($pdf->getFullPath()))->toBeTrue()
            ->and(file_get_contents($pdf->getFullPath(), length: 4))->toBe('%PDF');

        $this->get(route('budget.pdf.download', $pdf->download_token))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('Orcamento_'.$this->budget->code.'.pdf');

        expect($pdf->fresh()->download_count)->toBe(1);
    } finally {
        @unlink($pdf?->getFullPath());
    }
});

test('link com token expirado ou inválido não baixa o PDF', function () {
    Storage::fake('public');
    Storage::disk('public')->put('budgets/teste.pdf', '%PDF-1.4');

    $pdf = BudgetPdf::create([
        'budget_id' => $this->budget->id,
        'filename' => 'teste.pdf',
        'path' => 'budgets/teste.pdf',
        'download_token' => str_repeat('a', 64),
        'token_expires_at' => now()->subMinute(),
        'is_active' => true,
    ]);

    $this->get(route('budget.pdf.download', $pdf->download_token))->assertNotFound();
    $this->get(route('budget.pdf.download', 'token-invalido'))->assertNotFound();
});
