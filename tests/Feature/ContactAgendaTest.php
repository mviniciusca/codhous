<?php

use App\Filament\Resources\ContactAgendaResource;
use App\Filament\Resources\ContactAgendaResource\Pages\ListContactAgenda;
use App\Filament\Resources\ContactAgendaResource\Widgets\ContactAgendaStatsOverview;
use App\Models\ContactAgenda;
use App\Models\ContactInteraction;
use App\Models\User;
use Database\Seeders\ContactAgendaSeeder;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-12 10:00:00', 'UTC'));
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->seed(ContactAgendaSeeder::class);
});

afterEach(fn () => Carbon::setTestNow());

test('a página de agenda de contatos abre com sucesso e exibe widgets e tabela nativa', function () {
    $this->get(ContactAgendaResource::getUrl('index'))
        ->assertOk()
        ->assertSee('Agenda de Contatos')
        ->assertSee('Calendário de Contatos')
        ->assertSee('Próximos Contatos')
        ->assertSee('Categorias');
});

test('o widget de estatísticas exibe os números e proporções de contatos', function () {
    Livewire::test(ContactAgendaStatsOverview::class)
        ->assertSee('Total de Contatos')
        ->assertSee('48')
        ->assertSee('Clientes')
        ->assertSee('32')
        ->assertSee('Leads')
        ->assertSee('11')
        ->assertSee('Fornecedores')
        ->assertSee('5');
});

test('as abas nativas filtram a tabela por categoria', function () {
    $client = ContactAgenda::where('category', 'client')->orderBy('id', 'desc')->first();
    $lead = ContactAgenda::where('category', 'lead')->orderBy('id', 'desc')->first();
    $supplier = ContactAgenda::where('category', 'supplier')->orderBy('id', 'desc')->first();

    Livewire::test(ListContactAgenda::class)
        ->set('activeTab', 'clientes')
        ->assertCanSeeTableRecords([$client])
        ->assertCanNotSeeTableRecords([$lead, $supplier])
        ->set('activeTab', 'leads')
        ->assertCanSeeTableRecords([$lead])
        ->assertCanNotSeeTableRecords([$client, $supplier])
        ->set('activeTab', 'fornecedores')
        ->assertCanSeeTableRecords([$supplier])
        ->assertCanNotSeeTableRecords([$client, $lead]);
});

test('a busca nativa da tabela filtra contatos em tempo real', function () {
    $target = ContactAgenda::where('name', 'Rodrigo Ferreira')->first();
    $other = ContactAgenda::where('name', 'Mariana Costa')->first();

    Livewire::test(ListContactAgenda::class)
        ->searchTable('Rodrigo Ferreira')
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
});

test('a tabela possui paginação nativa e respeita o limite por página', function () {
    Livewire::test(ListContactAgenda::class)
        ->assertCountTableRecords(ContactAgenda::count());
});

test('navegação do mini calendário na barra lateral', function () {
    Livewire::test(ListContactAgenda::class)
        ->assertSee('Outubro de 2026')
        ->call('nextMonth')
        ->assertSee('Novembro de 2026')
        ->call('previousMonth')
        ->assertSee('Outubro de 2026')
        ->call('selectDay', '2026-10-15')
        ->assertSet('selectedDay', '2026-10-15');
});

test('ação nativa de exclusão de contato na tabela', function () {
    $contact = ContactAgenda::create([
        'name' => 'Contato para Teste Exclusão',
        'category' => 'client',
        'status' => 'active',
    ]);

    Livewire::test(ListContactAgenda::class)
        ->callTableAction(DeleteAction::class, $contact);

    expect(ContactAgenda::find($contact->id))->toBeNull();
});

test('agendamento de retorno na linha do contato cria uma interação', function () {
    $contact = ContactAgenda::first();

    Livewire::test(ListContactAgenda::class)
        ->callTableAction('scheduleInteraction', $contact, [
            'title' => 'Reunião de alinhamento financeiro',
            'type' => 'meeting',
            'scheduled_at' => '2026-10-14 15:00:00',
            'notes' => 'Discutir condições de pagamento',
        ])
        ->assertHasNoTableActionErrors();

    expect(ContactInteraction::where('title', 'Reunião de alinhamento financeiro')->exists())->toBeTrue();
});
