<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Workspace;
use App\Models\User;
use App\Models\WorkspaceEvent;
use App\Models\WorkspaceNote;
use App\Models\WorkspaceTask;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-12 13:00:00', 'UTC'));
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
    $this->actingAs($this->user);
});

afterEach(fn () => Carbon::setTestNow());

test('a página abre para qualquer usuário ativo, com os estados vazios', function () {
    $this->get(Workspace::getUrl())
        ->assertOk()
        ->assertSee('Meu Workspace')
        ->assertSee('Nenhuma tarefa pendente')
        ->assertSee('Agenda livre')
        ->assertDontSee('Nenhuma nota ainda');

    $this->get(Workspace::getUrl(['aba' => 'agenda']))
        ->assertOk()
        ->assertSee('Outubro 2026')
        ->assertSee('Nenhum compromisso neste dia');

    $this->get(Workspace::getUrl(['aba' => 'notas']))
        ->assertOk()
        ->assertSee('Nenhuma nota ainda');
});

test('aba inválida volta para Tarefas', function () {
    Livewire::test(Workspace::class)
        ->set('tab', 'lixo')
        ->assertSet('tab', 'tarefas');
});

test('perfil sem nenhuma área também acessa o próprio workspace', function () {
    $this->actingAs(User::factory()->withoutRole()->create())
        ->get(Workspace::getUrl())
        ->assertOk();
});

test('cria, edita, conclui e exclui uma tarefa', function () {
    Livewire::test(Workspace::class)
        ->callAction('task', ['title' => 'Revisar orçamento da obra', 'due_date' => '2026-10-12', 'due_time' => '10:00', 'priority' => 'high'])
        ->assertHasNoActionErrors()
        ->assertNotified('Tarefa adicionada')
        ->assertSee('Revisar orçamento da obra')
        ->assertSee('Hoje, 10:00');

    $task = $this->user->workspaceTasks()->sole();
    expect($task->priority->value)->toBe('high')->and($task->due_time)->toBe('10:00');

    Livewire::test(Workspace::class)
        ->callAction('task', ['title' => 'Revisar orçamento', 'due_date' => null, 'priority' => 'low'], ['id' => $task->id])
        ->assertNotified('Tarefa atualizada');

    $task->refresh();
    expect($task->title)->toBe('Revisar orçamento')
        ->and($task->due_date)->toBeNull()
        ->and($task->due_time)->toBeNull();

    Livewire::test(Workspace::class)
        ->call('toggleTask', $task->id)
        ->assertNotified('Tarefa concluída')
        ->assertDontSee('Revisar orçamento')
        ->set('taskFilter', 'done')
        ->assertSee('Revisar orçamento');

    expect($task->fresh()->isCompleted())->toBeTrue();

    Livewire::test(Workspace::class)
        ->callAction('deleteTask', [], ['id' => $task->id])
        ->assertNotified('Tarefa excluída');

    expect(WorkspaceTask::count())->toBe(0);
});

test('tarefa exige título e prioridade válida', function () {
    Livewire::test(Workspace::class)
        ->callAction('task', ['title' => '', 'priority' => 'urgente'])
        ->assertHasActionErrors(['title' => 'required', 'priority']);

    expect(WorkspaceTask::count())->toBe(0);
});

test('filtro Hoje traz as tarefas de hoje e as atrasadas', function () {
    $this->user->workspaceTasks()->createMany([
        ['title' => 'Atrasada', 'due_date' => '2026-10-10'],
        ['title' => 'De hoje', 'due_date' => '2026-10-12'],
        ['title' => 'Da semana que vem', 'due_date' => '2026-10-20'],
        ['title' => 'Sem prazo'],
    ]);

    Livewire::test(Workspace::class)
        ->set('taskFilter', 'today')
        ->assertSee(['Atrasada', 'De hoje'])
        ->assertDontSee(['Da semana que vem', 'Sem prazo']);
});

test('compromisso aparece no calendário e na lista de próximos', function () {
    Livewire::test(Workspace::class)
        ->callAction('event', [
            'title' => 'Apresentação para o cliente',
            'starts_at' => '14:30',
            'ends_at' => '16:00',
            'location' => 'Construtora Silva',
            'color' => 'purple',
        ], ['date' => '2026-10-14'])
        ->assertHasNoActionErrors()
        ->assertNotified('Compromisso adicionado')
        ->assertSet('day', '2026-10-14')
        ->assertSee('Apresentação para o cliente')
        ->assertSee('14:30 – 16:00')
        ->assertSee('Construtora Silva');

    $event = $this->user->workspaceEvents()->sole();
    expect($event->date->format('Y-m-d'))->toBe('2026-10-14')->and($event->color->value)->toBe('purple');

    // Último dia da grade (domingo, 1º de novembro) também entra na consulta
    $this->user->workspaceEvents()->create(['title' => 'Fim da grade', 'date' => '2026-11-01', 'all_day' => true]);
    Livewire::test(Workspace::class)->set('tab', 'agenda')->assertSee('Fim da grade');
});

test('fim do compromisso precisa ser depois do início, e dia inteiro dispensa horários', function () {
    Livewire::test(Workspace::class)
        ->callAction('event', ['title' => 'Visita', 'date' => '2026-10-12', 'starts_at' => '12:00', 'ends_at' => '10:00', 'color' => 'primary'])
        ->assertHasActionErrors(['ends_at']);

    Livewire::test(Workspace::class)
        ->callAction('event', ['title' => 'Visita', 'date' => '2026-10-12', 'starts_at' => null, 'color' => 'primary'])
        ->assertHasActionErrors(['starts_at' => 'required']);

    Livewire::test(Workspace::class)
        ->callAction('event', ['title' => 'Feira', 'date' => '2026-10-12', 'all_day' => true, 'color' => 'success'])
        ->assertHasNoActionErrors()
        ->assertSee('Dia inteiro');
});

test('navega entre meses e seleciona dias', function () {
    Livewire::test(Workspace::class)
        ->set('tab', 'agenda')
        ->call('nextMonth')
        ->assertSet('month', '2026-11')
        ->assertSee('Novembro 2026')
        ->call('previousMonth')
        ->assertSet('month', '')
        ->call('selectDay', '2026-12-01')
        ->assertSet('month', '2026-12')
        ->assertSee('Terça-feira, 1 de dezembro')
        ->call('goToToday')
        ->assertSet('month', '')
        ->assertSee('Hoje, 12 de outubro');

    Livewire::withQueryParams(['aba' => 'agenda', 'mes' => 'lixo'])->test(Workspace::class)->assertSee('Outubro 2026');
});

test('cria, edita e exclui notas pelo rodapé do modal', function () {
    Livewire::test(Workspace::class)
        ->assertSet('tab', 'tarefas')
        ->callAction('note', ['title' => 'Ideia para campanha', 'content' => 'Depoimentos de clientes', 'color' => 'warning'])
        ->assertNotified('Nota adicionada')
        ->assertSet('tab', 'notas')
        ->assertSee('Ideia para campanha')
        ->assertSee('Hoje, 10:00');

    $note = $this->user->workspaceNotes()->sole();

    Livewire::test(Workspace::class)
        ->callAction('note', ['title' => 'Campanha de outubro', 'content' => '', 'color' => 'gray'], ['id' => $note->id])
        ->assertNotified('Nota atualizada');

    expect($note->fresh())->title->toBe('Campanha de outubro')->content->toBeNull();

    Livewire::test(Workspace::class)
        ->mountAction(['note', 'removeNote'], ['note' => ['id' => $note->id]])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertSet('mountedActions', [])
        ->assertNotified('Nota excluída');

    expect(WorkspaceNote::count())->toBe(0);
});

test('cada usuário só vê e altera os próprios registros', function () {
    $task = $this->other->workspaceTasks()->create(['title' => 'Tarefa alheia']);
    $event = $this->other->workspaceEvents()->create(['title' => 'Compromisso alheio', 'date' => '2026-10-12', 'all_day' => true]);
    $note = $this->other->workspaceNotes()->create(['title' => 'Nota alheia']);

    foreach (array_keys(Workspace::TABS) as $tab) {
        Livewire::test(Workspace::class)
            ->set('tab', $tab)
            ->assertDontSee(['Tarefa alheia', 'Compromisso alheio', 'Nota alheia']);
    }

    expect(fn () => Livewire::test(Workspace::class)->call('toggleTask', $task->id))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => Livewire::test(Workspace::class)->callAction('deleteEvent', [], ['id' => $event->id]))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => Livewire::test(Workspace::class)->callAction('note', ['title' => 'Invadida', 'color' => 'gray'], ['id' => $note->id]))
        ->toThrow(ModelNotFoundException::class);

    expect($task->fresh()->isCompleted())->toBeFalse()
        ->and(WorkspaceEvent::find($event->id))->not->toBeNull()
        ->and($note->fresh()->title)->toBe('Nota alheia');
});

test('excluir o usuário apaga o workspace dele', function () {
    $this->other->workspaceNotes()->create(['title' => 'Nota']);
    $this->other->delete();

    expect(WorkspaceNote::count())->toBe(0);
});

test('atalhos do topo levam às abas e contam as tarefas de hoje e atrasadas', function () {
    $this->user->workspaceTasks()->createMany([
        ['title' => 'Atrasada', 'due_date' => '2026-10-10'],
        ['title' => 'De hoje', 'due_date' => '2026-10-12'],
        ['title' => 'Amanhã', 'due_date' => '2026-10-13'],
        ['title' => 'Feita', 'due_date' => '2026-10-12', 'completed_at' => now()],
    ]);
    $this->other->workspaceTasks()->create(['title' => 'De outro usuário', 'due_date' => '2026-10-12']);

    expect(Workspace::dueTaskCount())->toBe(2);

    $this->get(Dashboard::getUrl())
        ->assertOk()
        ->assertSee(Workspace::getUrl(['aba' => 'agenda']), escape: false)
        ->assertSee(Workspace::getUrl(['aba' => 'notas']), escape: false)
        ->assertSee('Minhas tarefas · 2 vencem hoje ou estão atrasadas');
});
