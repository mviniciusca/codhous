<?php

use App\Enums\ProductUnit;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\RelationManagers\ProductOptionRelationManager;
use App\Livewire\Budget as BudgetWizard;
use App\Models\Budget;
use App\Models\OperationArea;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Cache::flush();

    $this->concrete = Product::create(['name' => 'Concreto Usinado', 'unit' => ProductUnit::M3, 'min_quantity' => 1, 'is_active' => true, 'sort' => 2]);
    $this->fck25 = ProductOption::create(['product_id' => $this->concrete->id, 'name' => 'FCK 25', 'price' => 400, 'cost' => 300, 'is_active' => true]);

    $this->rental = Product::create(['name' => 'Aluguel de Betoneira', 'unit' => ProductUnit::DAY, 'min_quantity' => 1, 'is_active' => true, 'sort' => 1]);
    $this->mixer = ProductOption::create(['product_id' => $this->rental->id, 'name' => '400L', 'price' => 85, 'is_active' => true]);
});

function budgetWithItem(ProductOption $option, float $quantity = 2.5): Budget
{
    $budget = Budget::create(['user_id' => null, 'content' => [
        'customer_name' => 'Maria', 'customer_email' => 'maria@example.com', 'customer_phone' => '(21) 96645-2451',
        'postcode' => '22630-100', 'street' => 'Rua X', 'number' => '1', 'neighborhood' => 'Barra', 'city' => 'Rio', 'state' => 'RJ',
        'shipping' => 0, 'tax' => 0, 'discount' => 0,
    ]]);
    $budget->budgetItems()->create([
        'product_id' => $option->product_id, 'product_option_id' => $option->id,
        'quantity' => $quantity, 'price' => $option->price,
    ]);

    return $budget;
}

test('variação herda a unidade do produto e acompanha a troca', function () {
    expect($this->fck25->unit)->toBe(ProductUnit::M3);

    $this->concrete->update(['unit' => ProductUnit::KG]);

    expect($this->fck25->fresh()->unit)->toBe(ProductUnit::KG);
});

test('item do orçamento guarda nome, unidade, custo e subtotal da época', function () {
    $item = budgetWithItem($this->fck25)->budgetItems()->first();

    expect($item->product_name)->toBe('Concreto Usinado')
        ->and($item->option_name)->toBe('FCK 25')
        ->and($item->unit)->toBe('m³')
        ->and((float) $item->unit_cost)->toBe(300.0)
        ->and((float) $item->subtotal)->toBe(1000.0)
        ->and($item->grossProfit())->toBe(250.0);
});

test('renomear, reajustar ou arquivar a variação não altera orçamentos antigos', function () {
    $budget = budgetWithItem($this->fck25);

    $this->fck25->update(['name' => 'FCK 25 Bombeável', 'price' => 450, 'cost' => 320]);
    $this->fck25->delete();

    $item = $budget->budgetItems()->first();
    $html = view('pdf.invoice', ['state' => $budget->toArray(), 'budget' => $budget, 'company' => null, 'layout' => null])->render();

    expect($item->describe())->toBe('Concreto Usinado · FCK 25')
        ->and((float) $item->price)->toBe(400.0)
        ->and((float) $item->unit_cost)->toBe(300.0)
        ->and($item->product_option_id)->toBe($this->fck25->id)
        ->and($html)->toContain('FCK 25')->not->toContain('Bombeável')->toContain('R$ 1.000,00');
});

test('produto usado em orçamentos não pode ser excluído definitivamente', function () {
    budgetWithItem($this->fck25);
    $this->concrete->delete();

    expect(fn () => $this->concrete->forceDelete())->toThrow(ValidationException::class);
    expect(Product::withTrashed()->find($this->concrete->id))->not->toBeNull();
});

test('orçamento antigo com variação arquivada continua editável no admin', function () {
    $this->actingAs(User::factory()->create());
    $budget = budgetWithItem($this->fck25);
    $this->fck25->delete();

    Livewire::test(EditBudget::class, ['record' => $budget->getRouteKey()])
        ->assertSee('(arquivada)')
        ->call('save')
        ->assertHasNoFormErrors();
});

test('admin vê a margem estimada do orçamento', function () {
    $this->actingAs(User::factory()->create());
    $budget = budgetWithItem($this->fck25);

    Livewire::test(EditBudget::class, ['record' => $budget->getRouteKey()])
        ->assertSee('Margem')
        ->assertSee('R$ 250,00')
        ->assertSee('25,0% sobre os itens');
});

test('site segue a ordem do catálogo e esconde variações inativas', function () {
    $this->mixer->update(['is_active' => false]);
    ProductOption::create(['product_id' => $this->rental->id, 'name' => 'Placa vibratória', 'price' => 120, 'is_active' => true]);

    $component = Livewire::test(BudgetWizard::class);

    expect(array_values($component->viewData('allProducts')))->toBe(['Aluguel de Betoneira', 'Concreto Usinado'])
        ->and($component->instance()->getOptionsForProduct($this->rental->id))->not->toContain('400L');
});

test('aluguel por dia não aceita fração; concreto aceita', function () {
    Http::fake(['*' => Http::response(['logradouro' => 'Rua X', 'bairro' => 'Barra', 'localidade' => 'Rio', 'uf' => 'RJ'])]);
    OperationArea::create(['city' => 'Rio', 'state' => 'RJ', 'postcode_prefix' => '20000', 'postcode_start' => '20000', 'postcode_end' => '23999', 'is_active' => true, 'shipping_fee' => 0]);

    Livewire::test(BudgetWizard::class)
        ->set('items.0.product_id', $this->rental->id)
        ->set('items.0.option_id', $this->mixer->id)
        ->set('items.0.quantity', 1.5)
        ->assertSet('items.0.quantity', 2.0)
        ->set('items.0.product_id', $this->concrete->id)
        ->set('items.0.option_id', $this->fck25->id)
        ->set('items.0.quantity', 2.5)
        ->assertSet('items.0.quantity', 2.5);
});

test('produto é criado já com as variações', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'name' => 'Polimento de Piso',
            'unit' => ProductUnit::M2->value,
            'min_quantity' => 20,
            'is_active' => true,
            'productOption' => [
                ['name' => 'Básico', 'price' => '28,00', 'cost' => '15,50'],
                ['name' => 'Espelhado', 'price' => '48,00', 'cost' => null],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $product = Product::where('name', 'Polimento de Piso')->first();

    expect($product->productOption)->toHaveCount(2)
        ->and((float) $product->productOption->firstWhere('name', 'Básico')->cost)->toBe(15.5)
        ->and($product->productOption->first()->unit)->toBe(ProductUnit::M2)
        ->and($product->sort)->toBe(3);
});

test('reajuste em lote aplica percentual com arredondamento e registra o histórico', function () {
    $this->actingAs(User::factory()->create());
    $fck30 = ProductOption::create(['product_id' => $this->concrete->id, 'name' => 'FCK 30', 'price' => 445, 'is_active' => true]);

    Livewire::test(ProductOptionRelationManager::class, ['ownerRecord' => $this->concrete, 'pageClass' => EditProduct::class])
        ->callTableBulkAction('adjust_prices', [$this->fck25, $fck30], data: ['percent' => 8, 'target' => 'price', 'rounding' => '1'])
        ->assertHasNoTableBulkActionErrors();

    expect((float) $this->fck25->fresh()->price)->toBe(432.0)   // 400 × 1,08
        ->and((float) $fck30->fresh()->price)->toBe(481.0)      // 480,60 → 481
        ->and((float) $this->fck25->fresh()->cost)->toBe(300.0);

    Livewire::test(ProductOptionRelationManager::class, ['ownerRecord' => $this->concrete, 'pageClass' => EditProduct::class])
        ->mountTableAction('price_history', $this->fck25)
        ->assertSee('Preço: R$ 400,00 → R$ 432,00');
});

test('desativar variação pela lista a remove do site', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ProductOptionRelationManager::class, ['ownerRecord' => $this->concrete, 'pageClass' => EditProduct::class])
        ->call('updateTableColumnState', 'is_active', (string) $this->fck25->id, false);

    expect($this->fck25->fresh()->is_active)->toBeFalse()
        ->and($this->concrete->fresh()->isSellable())->toBeFalse();
});

test('lista de produtos mostra unidade, faixa de preço, margem e visibilidade', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ListProducts::class)
        ->assertSee('R$ 400,00 /m³')
        ->assertSee('25,0%')
        ->assertSee('Visível')
        ->assertCanSeeTableRecords([$this->rental, $this->concrete], inOrder: true);
});
