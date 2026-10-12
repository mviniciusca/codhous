<?php

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\BudgetResource\Pages\ListBudgets;
use App\Filament\Resources\BudgetResource\Widgets\BudgetStatsOverview;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Number;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $make = fn (string $name, string $status, float $total, array $extra = []) => Budget::create(array_merge([
        'user_id' => null,
        'status' => $status,
        'content' => ['customer_name' => $name, 'customer_phone' => '21966452451', 'city' => 'Niterói', 'total' => $total],
    ], $extra));

    $this->new = $make('Ana Nova', 'pending', 1000);
    $this->negotiating = $make('Bruno Negocia', 'on going', 2500);
    $this->closed = $make('Carla Fechou', 'done', 4000);
    $this->archived = $make('Davi Arquivou', 'ignored', 300);
});

test('aba padrão mostra só os orçamentos em aberto', function () {
    Livewire::test(ListBudgets::class)
        ->assertCanSeeTableRecords([$this->new, $this->negotiating])
        ->assertCanNotSeeTableRecords([$this->closed, $this->archived]);
});

test('abas filtram por etapa', function () {
    Livewire::test(ListBudgets::class)
        ->set('activeTab', 'fechados')
        ->assertCanSeeTableRecords([$this->closed])
        ->assertCanNotSeeTableRecords([$this->new, $this->negotiating, $this->archived])
        ->set('activeTab', 'todos')
        ->assertCanSeeTableRecords([$this->new, $this->negotiating, $this->closed, $this->archived]);
});

test('busca encontra pelo nome do cliente e pela cidade', function () {
    Livewire::test(ListBudgets::class)
        ->searchTable('Bruno')
        ->assertCanSeeTableRecords([$this->negotiating])
        ->assertCanNotSeeTableRecords([$this->new])
        ->searchTable('Niterói')
        ->assertCanSeeTableRecords([$this->new, $this->negotiating]);
});

test('lista ordena pelo valor total', function () {
    Livewire::test(ListBudgets::class)
        ->set('activeTab', 'todos')
        ->sortTable('total', 'desc')
        ->assertCanSeeTableRecords([$this->closed, $this->negotiating, $this->new, $this->archived], inOrder: true);
});

test('valores e telefone aparecem no formato brasileiro', function () {
    Livewire::test(ListBudgets::class)
        ->assertSee(Number::currency(2500, 'BRL', 'pt_BR')) // "R$ 2.500,00"
        ->assertSee('(21) 96645-2451')
        ->assertSee('Em negociação');
});

test('orçamentos na lixeira não aparecem na lista', function () {
    $this->new->delete();

    Livewire::test(ListBudgets::class)
        ->assertCanNotSeeTableRecords([$this->new]);
});

test('ação de linha move o orçamento de etapa', function () {
    Livewire::test(ListBudgets::class)
        ->callTableAction('move_done', $this->new);

    expect($this->new->fresh()->status)->toBe('done')
        ->and($this->new->fresh()->status_changed_at)->not->toBeNull();
});

test('ação em massa move vários orçamentos', function () {
    Livewire::test(ListBudgets::class)
        ->callTableBulkAction('move_status', [$this->new, $this->negotiating], data: ['status' => 'ignored']);

    expect($this->new->fresh()->status)->toBe('ignored')
        ->and($this->negotiating->fresh()->status)->toBe('ignored');
});

test('baixar PDF pela lista não cria link público', function () {
    Livewire::test(ListBudgets::class)
        ->callTableAction('download_pdf', $this->new)
        ->assertFileDownloaded('Orcamento_'.$this->new->code.'.pdf');

    expect($this->new->pdfs()->count())->toBe(0);
});

test('indicadores somam pela etapa e usam a data da mudança de etapa', function () {
    // Fechado no mês passado não entra em "Fechados em <mês>"
    Budget::create([
        'user_id' => null, 'status' => 'done', 'status_changed_at' => now()->subMonths(4),
        'content' => ['customer_name' => 'Antigo', 'total' => 9999],
    ]);

    Livewire::test(BudgetStatsOverview::class)
        ->assertSee('Novos pedidos')
        ->assertSee('R$ 2.500,00')   // em negociação
        ->assertSee('R$ 4.000,00')   // fechados no mês
        ->assertDontSee('R$ 13.999,00')
        ->assertSee('1 fechado de 2 decididos');
});

test('badge do menu conta só os novos pedidos', function () {
    expect(BudgetResource::getNavigationBadge())->toBe('1');
});

test('troca de etapa registra quando aconteceu', function () {
    $this->travel(2)->days();
    $this->negotiating->update(['status' => 'done']);

    expect($this->negotiating->fresh()->status_changed_at->isToday())->toBeTrue()
        ->and($this->negotiating->fresh()->daysInStage())->toBe(0);
});

test('exportação para Excel gera a planilha dos selecionados', function () {
    Livewire::test(ListBudgets::class)
        ->callTableBulkAction('export', [$this->new, $this->negotiating])
        ->assertHasNoErrors()
        ->assertFileDownloaded();
});
