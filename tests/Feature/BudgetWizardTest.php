<?php

use App\Enums\ProductUnit;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Livewire\Budget as BudgetWizard;
use App\Mail\AdminNewBudgetMail;
use App\Mail\CustomerBudgetReceivedMail;
use App\Models\Budget;
use App\Models\OperationArea;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Cache::flush();
    RateLimiter::clear('budget-submit:127.0.0.1');

    Http::fake([
        '*' => Http::response([
            'cep' => '22630-100',
            'logradouro' => 'Rua Ulisses Alcântara',
            'bairro' => 'Barra da Tijuca',
            'localidade' => 'Rio de Janeiro',
            'uf' => 'RJ',
        ]),
    ]);

    OperationArea::create([
        'city' => 'Rio de Janeiro',
        'state' => 'RJ',
        'postcode_prefix' => '20000',
        'postcode_start' => '20000',
        'postcode_end' => '23999',
        'is_active' => true,
        'shipping_fee' => 150,
    ]);

    $this->product = Product::create(['name' => 'Concreto Usinado', 'is_active' => true, 'min_quantity' => 1]);
    $this->option = ProductOption::create([
        'product_id' => $this->product->id,
        'name' => 'FCK 25',
        'slug' => 'fck-25',
        'unit' => ProductUnit::M3,
        'price' => 400,
        'is_active' => true,
    ]);
});

/**
 * Preenche o wizard até a etapa de revisão.
 */
function fillWizard(array $overrides = [])
{
    $test = test();

    $component = Livewire::test(BudgetWizard::class)
        ->set('items.0.product_id', $overrides['product_id'] ?? $test->product->id)
        ->set('items.0.option_id', $overrides['option_id'] ?? $test->option->id)
        ->set('items.0.quantity', $overrides['quantity'] ?? 2.5)
        ->set('customer_name', 'Maria da Silva')
        ->set('customer_phone', $overrides['customer_phone'] ?? '21966452451')
        ->set('customer_email', 'maria@example.com')
        ->set('postcode', $overrides['postcode'] ?? '22630-100')
        ->set('number', '122')
        ->set('delivery_period', 'manha')
        ->set('needs_pump', 'sim');

    return $component;
}

test('cliente envia orçamento pelo site e o pedido é salvo com itens, frete e e-mails', function () {
    fillWizard()
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('isSubmitted', true);

    $budget = Budget::with('budgetItems')->sole();

    expect($budget->code)->toStartWith('OR-'.date('Y'))
        ->and($budget->user_id)->toBeNull()
        ->and($budget->status)->toBe('pending')
        ->and($budget->content['customer_phone'])->toBe('(21) 96645-2451')
        ->and($budget->content['street'])->toBe('Rua Ulisses Alcântara')
        ->and((float) $budget->content['shipping'])->toBe(150.0)
        ->and((float) $budget->content['subtotal'])->toBe(1000.0) // 2,5 m³ x R$ 400
        ->and((float) $budget->content['total'])->toBe(1150.0)
        ->and($budget->content['tax'])->toBe(0)
        ->and($budget->content['discount'])->toBe(0)
        ->and($budget->content['delivery_period'])->toBe('manha')
        ->and($budget->content['needs_pump'])->toBe('sim')
        ->and($budget->budgetItems)->toHaveCount(1)
        ->and((float) $budget->budgetItems->first()->quantity)->toBe(2.5);

    Mail::assertSent(AdminNewBudgetMail::class);
    Mail::assertSent(CustomerBudgetReceivedMail::class, fn ($mail) => $mail->hasTo('maria@example.com'));
});

test('pedido do site não é atribuído ao usuário logado no admin', function () {
    $this->actingAs(User::factory()->create());

    fillWizard()->call('submit')->assertHasNoErrors();

    expect(Budget::sole()->user_id)->toBeNull();
});

test('preço manipulado no navegador é ignorado e recalculado pelo banco', function () {
    fillWizard()
        ->set('items.0.price', 0.01)
        ->call('submit')
        ->assertHasNoErrors();

    $budget = Budget::with('budgetItems')->sole();

    expect((float) $budget->budgetItems->first()->price)->toBe(400.0)
        ->and((float) $budget->content['subtotal'])->toBe(1000.0);
});

test('totais calculados no servidor não podem ser alterados pelo navegador', function () {
    expect(fn () => fillWizard()->set('total', 1))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

test('submit valida os itens mesmo quando as etapas são puladas', function () {
    Livewire::test(BudgetWizard::class)
        ->set('customer_name', 'Maria')
        ->set('customer_phone', '21966452451')
        ->set('customer_email', 'maria@example.com')
        ->set('postcode', '22630-100')
        ->set('number', '10')
        ->call('submit')
        ->assertHasErrors(['items.0.product_id', 'items.0.option_id']);

    expect(Budget::count())->toBe(0);
});

test('opção de outro produto ou desativada é recusada', function () {
    $other = Product::create(['name' => 'Polimento de Piso', 'is_active' => true]);
    $foreignOption = ProductOption::create([
        'product_id' => $other->id, 'name' => 'Polido', 'slug' => 'polido',
        'unit' => ProductUnit::M2, 'price' => 30, 'is_active' => true,
    ]);

    fillWizard(['option_id' => $foreignOption->id])
        ->call('submit')
        ->assertHasErrors(['items.0.option_id']);

    $this->option->update(['is_active' => false]);

    fillWizard()
        ->call('submit')
        ->assertHasErrors(['items.0.option_id']);

    expect(Budget::count())->toBe(0);
});

test('produto desativado não aparece no site e não é aceito', function () {
    $this->product->update(['is_active' => false]);

    fillWizard()
        ->assertViewHas('allProducts', fn ($products) => ! array_key_exists($this->product->id, $products))
        ->call('submit')
        ->assertHasErrors(['items.0.product_id']);
});

test('quantidade abaixo do mínimo do produto é recusada', function () {
    $this->product->update(['min_quantity' => 3]);

    fillWizard()
        ->set('items.0.quantity', 1)
        ->assertSet('items.0.quantity', 3.0);
});

test('CEP fora da área de atendimento é recusado', function () {
    fillWizard(['postcode' => '01310-100'])
        ->call('submit')
        ->assertHasErrors(['postcode']);

    expect(Budget::count())->toBe(0);
});

test('telefone inválido é recusado e telefone fixo é aceito', function () {
    fillWizard(['customer_phone' => '2199'])
        ->call('submit')
        ->assertHasErrors(['customer_phone']);

    fillWizard(['customer_phone' => '2133334444'])
        ->call('submit')
        ->assertHasNoErrors();

    expect(Budget::sole()->content['customer_phone'])->toBe('(21) 3333-4444');
});

test('data de entrega no passado é recusada', function () {
    fillWizard()
        ->set('delivery_date', now()->subDay()->toDateString())
        ->call('submit')
        ->assertHasErrors(['delivery_date']);
});

test('falha no envio de e-mail não impede o pedido de ser salvo', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP fora do ar'));

    fillWizard()
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('isSubmitted', true);

    expect(Budget::count())->toBe(1);
});

test('envios em excesso do mesmo IP são bloqueados', function () {
    foreach (range(1, 5) as $i) {
        fillWizard()->call('submit')->assertHasNoErrors();
    }

    fillWizard()->call('submit')->assertHasErrors(['submit']);

    expect(Budget::count())->toBe(5);
});

test('pedidos simultâneos recebem códigos diferentes', function () {
    fillWizard()->call('submit');
    fillWizard()->call('submit');

    expect(Budget::pluck('code')->unique())->toHaveCount(2);
});

test('alterar o preço no admin limpa o cache do site', function () {
    $component = fillWizard();
    expect((float) $component->get('items.0.price'))->toBe(400.0);

    $this->option->update(['price' => 450]);

    fillWizard()
        ->call('submit')
        ->assertHasNoErrors();

    expect((float) Budget::with('budgetItems')->sole()->budgetItems->first()->price)->toBe(450.0);
});

test('admin consegue salvar orçamento vindo do site sem preencher taxa e desconto', function () {
    fillWizard()->call('submit');
    $budget = Budget::sole();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditBudget::class, ['record' => $budget->getRouteKey()])
        ->assertFormSet(['content.tax' => '0,00', 'content.discount' => '0,00'])
        ->call('save')
        ->assertHasNoFormErrors();

    $budget->refresh();

    expect((float) $budget->content['total'])->toBe(1150.0)
        ->and((float) $budget->budgetItems()->first()->quantity)->toBe(2.5)
        ->and($budget->content['customer_phone'])->toBe('(21) 96645-2451')
        ->and($budget->content['needs_pump'])->toBe('sim');
});
