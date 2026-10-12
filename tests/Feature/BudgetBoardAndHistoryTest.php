<?php

use App\Enums\ProductUnit;
use App\Filament\Resources\BudgetResource\Pages\BudgetBoard;
use App\Filament\Resources\BudgetResource\Pages\CreateBudget;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Models\Budget;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Services\BudgetTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['name' => 'Ana Vendas']));

    $product = Product::create(['name' => 'Concreto Usinado', 'is_active' => true]);
    $this->option = ProductOption::create([
        'product_id' => $product->id, 'name' => 'FCK 25', 'slug' => 'fck-25',
        'unit' => ProductUnit::M3, 'price' => 400, 'is_active' => true,
    ]);

    $this->budget = Budget::create([
        'user_id' => null,
        'content' => [
            'customer_name' => 'Maria da Silva', 'customer_email' => 'maria@example.com',
            'customer_phone' => '(21) 96645-2451', 'postcode' => '22630-100', 'street' => 'Rua X',
            'number' => '10', 'neighborhood' => 'Barra', 'city' => 'Rio de Janeiro', 'state' => 'RJ',
            'shipping' => 150, 'tax' => 0, 'discount' => 0, 'subtotal' => 1000, 'total' => 1150,
        ],
    ]);
    $this->budget->budgetItems()->create([
        'product_id' => $product->id, 'product_option_id' => $this->option->id,
        'quantity' => 2.5, 'price' => 400, 'subtotal' => 1000,
    ]);
});

function timelineEntries(Budget $budget): Collection
{
    return (new BudgetTimeline($budget))->grouped(50)->flatten(1);
}

test('pedido do site aparece no histórico com o cliente e o total', function () {
    $entry = timelineEntries($this->budget)->last();

    expect($entry['title'])->toBe('Pedido recebido pelo site')
        ->and($entry['actor'])->toBe('Cliente')
        ->and($entry['details'][0])->toContain('Maria da Silva')
        ->and($entry['details'][0])->toContain('R$ 1.150,00');
});

test('alterar preço do item no admin registra antes e depois no histórico', function () {
    $this->travel(1)->minute();
    $itemKey = 'record-'.$this->budget->budgetItems()->first()->id;

    Livewire::test(EditBudget::class, ['record' => $this->budget->getRouteKey()])
        ->set("data.budgetItems.{$itemKey}.price", '380,00')
        ->set('data.content.discount', '50,00')
        ->call('save')
        ->assertHasNoFormErrors();

    $entries = timelineEntries($this->budget->fresh());
    $item = $entries->firstWhere('title', 'Item alterado');
    $values = $entries->firstWhere('title', 'Valores atualizados');

    expect($item['actor'])->toBe('Ana Vendas')
        ->and($item['details'][0])->toBe('Concreto Usinado · FCK 25')
        ->and($item['changes'][0])->toMatchArray(['label' => 'Preço unitário', 'old' => 'R$ 400,00', 'new' => 'R$ 380,00', 'delta' => '−R$ 20,00'])
        ->and(collect($values['changes'])->firstWhere('label', 'Desconto'))->toMatchArray(['old' => 'R$ 0,00', 'new' => 'R$ 50,00']);
});

test('salvar sem mudar nada não polui o histórico', function () {
    $this->travel(1)->minute();
    $before = (new BudgetTimeline($this->budget))->total();

    Livewire::test(EditBudget::class, ['record' => $this->budget->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    $titles = timelineEntries($this->budget->fresh())->pluck('title');

    expect($titles)->not->toContain('Item alterado')
        ->and($titles->filter(fn ($t) => $t === 'Valores atualizados'))->toHaveCount(0);
});

test('troca de etapa aparece uma única vez e em português', function () {
    Livewire::test(EditBudget::class, ['record' => $this->budget->getRouteKey()])
        ->call('setStatus', 'on going')
        ->assertSee('Etapa alterada');

    $status = timelineEntries($this->budget->fresh())->where('title', 'Etapa alterada');

    expect($status)->toHaveCount(1)
        ->and($status->first()['changes'][0])->toMatchArray(['old' => 'Novo pedido', 'new' => 'Em negociação']);
});

test('item removido e adicionado depois da criação ficam registrados', function () {
    $this->travel(1)->minute();

    $item = $this->budget->budgetItems()->first();
    $item->delete();
    $this->budget->budgetItems()->create([
        'product_id' => $item->product_id, 'product_option_id' => $this->option->id,
        'quantity' => 3, 'price' => 400, 'subtotal' => 1200,
    ]);

    $titles = timelineEntries($this->budget->fresh())->pluck('title');

    expect($titles)->toContain('Item removido')->toContain('Item adicionado');
});

test('quadro mostra os orçamentos por etapa e permite mover', function () {
    Livewire::test(BudgetBoard::class)
        ->assertSee($this->budget->code)
        ->assertSee('Maria da Silva')
        ->assertSee('R$ 1.150,00')
        ->call('moveBudget', $this->budget->id, 'done');

    expect($this->budget->fresh()->status)->toBe('done');
});

test('quadro ignora etapa inválida e filtra pela busca', function () {
    $other = Budget::create(['user_id' => null, 'content' => ['customer_name' => 'João Pereira', 'total' => 10]]);

    Livewire::test(BudgetBoard::class)
        ->call('moveBudget', $this->budget->id, 'hacked')
        ->set('search', 'João')
        ->assertSee('João Pereira')
        ->assertDontSee('Maria da Silva');

    expect($this->budget->fresh()->status)->toBe('pending');
});

test('novo orçamento começa com valores formatados e telefone sem máscara vazia', function () {
    Livewire::test(CreateBudget::class)
        ->assertFormSet([
            'content.shipping' => '0,00',
            'content.tax' => '0,00',
            'content.discount' => '0,00',
        ])
        ->assertSeeHtml("length === 0 ? ''");
});
