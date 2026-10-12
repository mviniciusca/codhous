<?php

use App\Enums\ProductUnit;
use App\Filament\Pages\Dashboard;
use App\Models\Budget;
use App\Models\Mail;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Services\DashboardOverview;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Quarta-feira, 10h em São Paulo
    $this->travelTo(Carbon::parse('2026-10-14 10:00', 'America/Sao_Paulo'));
    $this->actingAs(User::factory()->create(['name' => 'Marcos Coelho']));

    $concrete = Product::create(['name' => 'Concreto Bombeado', 'unit' => ProductUnit::M3, 'min_quantity' => 3, 'is_active' => true]);
    $this->fck25 = ProductOption::create(['product_id' => $concrete->id, 'name' => 'FCK 25', 'price' => 400, 'is_active' => true]);

    $this->budget = function (string $name, string $status, array $content = [], float $quantity = 0) {
        $budget = Budget::create([
            'user_id' => null,
            'status' => $status,
            'content' => array_merge(['customer_name' => $name, 'neighborhood' => 'Barra', 'city' => 'Rio de Janeiro', 'total' => 0], $content),
        ]);

        if ($quantity > 0) {
            $budget->budgetItems()->create([
                'product_id' => $this->fck25->product_id,
                'product_option_id' => $this->fck25->id,
                'quantity' => $quantity,
                'price' => $this->fck25->price,
            ]);
        }

        return $budget;
    };
});

test('visão geral abre com saudação, agenda e pendências', function () {
    Livewire::test(Dashboard::class)
        ->assertOk()
        ->assertSee('Bom dia, Marcos')
        ->assertSee('Agenda de entregas')
        ->assertSee('Atenção')
        ->assertSee('Orçamentos recentes')
        ->assertSee('Tudo em dia');
});

test('agenda agrupa entregas dos próximos 7 dias com volume em m³', function () {
    ($this->budget)('Obra Horizonte', 'done', ['delivery_date' => '2026-10-14', 'delivery_period' => 'manha', 'needs_pump' => 'sim'], 18);
    ($this->budget)('Residencial Parque', 'on going', ['delivery_date' => '2026-10-14', 'delivery_period' => 'tarde'], 12);
    ($this->budget)('Fora da janela', 'done', ['delivery_date' => '2026-10-21'], 5);
    ($this->budget)('Arquivado', 'ignored', ['delivery_date' => '2026-10-14'], 30);

    $agenda = app(DashboardOverview::class)->agenda();
    $today = $agenda['today'];

    expect($agenda['days'])->toHaveCount(DashboardOverview::AGENDA_DAYS)
        ->and($today['weekday'])->toBe('Hoje')
        ->and($today['count'])->toBe(2)
        ->and($today['volume'])->toBe(30.0)
        ->and($today['confirmed'])->toBe(1)
        ->and(collect($today['entries'])->pluck('customer')->all())->toBe(['Obra Horizonte', 'Residencial Parque'])
        ->and($today['entries'][0]['pump'])->toBe('Com bomba')
        ->and(collect($agenda['days'])->flatMap(fn ($day) => $day['entries'])->pluck('customer'))->not->toContain('Fora da janela', 'Arquivado');

    Livewire::test(Dashboard::class)
        ->assertSee('2 entregas agendadas para hoje (30 m³)')
        ->assertSee('Obra Horizonte');
});

test('pendências contam entregas vencidas, negociações paradas e mensagens não lidas', function () {
    ($this->budget)('Atrasado', 'pending', ['delivery_date' => '2026-10-10']);
    ($this->budget)('Já entregue', 'done', ['delivery_date' => '2026-10-10']);

    $stale = ($this->budget)('Parado', 'on going');
    $stale->forceFill(['status_changed_at' => now()->subDays(Budget::STALE_AFTER_DAYS + 1)])->saveQuietly();

    Mail::factory()->create(['is_read' => false, 'is_sent' => false, 'is_spam' => false]);
    Mail::factory()->create(['is_read' => false, 'is_sent' => false, 'is_spam' => true]);

    $attention = collect(app(DashboardOverview::class)->attention())->keyBy('title');

    expect($attention['entrega com data vencida']['count'])->toBe(1)
        ->and($attention['negociação parada']['count'])->toBe(1)
        ->and($attention['mensagem não lida']['count'])->toBe(1);

    Livewire::test(Dashboard::class)->assertDontSee('Tudo em dia');
});

test('indicadores somam o fechado no mês e comparam com o mesmo período do mês anterior', function () {
    ($this->budget)('Fechou agora', 'done', ['total' => '4.000,00'], 10);

    $previous = ($this->budget)('Fechou mês passado', 'done', ['total' => 2000]);
    $previous->forceFill(['status_changed_at' => now()->subMonthNoOverflow()->subHour()])->saveQuietly();

    $tooLate = ($this->budget)('Fechou no fim do mês passado', 'done', ['total' => 9000]);
    $tooLate->forceFill(['status_changed_at' => Carbon::parse('2026-09-28 12:00', 'America/Sao_Paulo')])->saveQuietly();

    $closed = app(DashboardOverview::class)->kpis()['closed'];

    expect($closed['value'])->toBe('R$ 4.000,00')
        ->and($closed['note'])->toBe('1 pedido · 10 m³')
        ->and($closed['delta'])->toBe(100);
});

test('ranking de produtos usa só orçamentos fechados', function () {
    ($this->budget)('Fechado', 'done', [], 10);
    ($this->budget)('Em negociação', 'on going', [], 50);

    $ranking = app(DashboardOverview::class)->topProducts();

    expect($ranking)->toHaveCount(1)
        ->and($ranking[0]['name'])->toBe('Concreto Bombeado')
        ->and($ranking[0]['revenueLabel'])->toBe('R$ 4.000,00')
        ->and($ranking[0]['quantityLabel'])->toBe('10 m³');
});

test('cotação rápida abre um orçamento novo com os itens calculados', function () {
    $undoRepeaterFake = Repeater::fake();

    Livewire::test(Dashboard::class)
        ->callAction('quickQuote', data: [
            'items' => [
                ['product' => $this->fck25->product_id, 'product_option' => $this->fck25->id, 'quantity' => 5, 'price' => 400],
            ],
        ])
        ->assertHasNoActionErrors()
        ->assertRedirectContains('source=calculator');

    $undoRepeaterFake();
});
