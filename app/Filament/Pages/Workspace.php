<?php

namespace App\Filament\Pages;

use App\Enums\WorkspaceColor;
use App\Enums\WorkspacePriority;
use App\Models\WorkspaceEvent;
use App\Models\WorkspaceNote;
use App\Models\WorkspaceTask;
use App\Support\WorkspaceDates;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * Meu Workspace: calendário, tarefas e notas rápidas do próprio usuário.
 * Tudo é pessoal: cada consulta passa por ownedBy(auth()->user()).
 */
class Workspace extends Page
{
    protected static ?string $navigationLabel = 'Meu Workspace';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $activeNavigationIcon = 'heroicon-s-calendar-days';

    /** Logo depois da Visão geral (-2) */
    protected static ?int $navigationSort = -1;

    protected static ?string $slug = 'workspace';

    protected static string $view = 'filament.workspace.index';

    /** Abas da página: cada área ganha a largura toda */
    public const TABS = [
        'tarefas' => ['label' => 'Tarefas', 'icon' => 'heroicon-m-check-circle'],
        'agenda' => ['label' => 'Agenda', 'icon' => 'heroicon-m-calendar-days'],
        'notas' => ['label' => 'Notas', 'icon' => 'heroicon-m-document-text'],
    ];

    public const TASK_FILTERS = [
        'pending' => 'Pendentes',
        'today' => 'Hoje',
        'done' => 'Concluídas',
    ];

    protected const DONE_LIMIT = 30;

    protected const UPCOMING_LIMIT = 4;

    #[Url(as: 'aba', except: 'tarefas')]
    public string $tab = 'tarefas';

    /** Mês visível no calendário (Y-m); vazio = mês atual */
    #[Url(as: 'mes', except: '')]
    public string $month = '';

    /** Dia selecionado no calendário (Y-m-d); vazio = hoje */
    public string $day = '';

    public string $taskFilter = 'pending';

    public function getTitle(): string|Htmlable
    {
        return 'Meu Workspace';
    }

    public function getSubheading(): ?string
    {
        return 'Organize suas tarefas, ideias e compromissos em um só lugar.';
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                $this->taskAction()->label('Tarefa')->icon('heroicon-o-check-circle'),
                $this->eventAction()->label('Compromisso')->icon('heroicon-o-calendar'),
                $this->noteAction()->label('Nota rápida')->icon('heroicon-o-document-text'),
            ])
                ->label('Novo')
                ->icon('heroicon-m-plus')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }

    public function updatedTab(): void
    {
        if (! array_key_exists($this->tab, self::TABS)) {
            $this->tab = 'tarefas';
        }
    }

    /** Depois de salvar, leva à aba onde o item aparece (se ele não estiver visível na atual) */
    protected function reveal(string $tab, array $alsoVisibleOn = []): void
    {
        if ($this->tab !== $tab && ! in_array($this->tab, $alsoVisibleOn, true)) {
            $this->tab = $tab;
        }
    }

    // ------------------------------------------------------------------
    // Calendário
    // ------------------------------------------------------------------

    public function previousMonth(): void
    {
        $this->goToMonth($this->visibleMonth()->subMonth());
    }

    public function nextMonth(): void
    {
        $this->goToMonth($this->visibleMonth()->addMonth());
    }

    public function goToToday(): void
    {
        $this->month = '';
        $this->day = '';
    }

    public function selectDay(string $date): void
    {
        $day = $this->parseDate($date);

        if (! $day) {
            return;
        }

        $this->day = $day->toDateString();

        // Clicar num dia de outro mês (bordas da grade) leva até ele
        if ($day->format('Y-m') !== $this->visibleMonth()->format('Y-m')) {
            $this->month = $day->format('Y-m');
        }
    }

    protected function goToMonth(CarbonImmutable $month): void
    {
        $today = WorkspaceDates::today();

        $this->month = $month->isSameMonth($today) ? '' : $month->format('Y-m');
        $this->day = $month->isSameMonth($today) ? '' : $month->startOfMonth()->toDateString();
    }

    public function visibleMonth(): CarbonImmutable
    {
        $month = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $this->month)
            ? CarbonImmutable::createFromFormat('!Y-m', $this->month, WorkspaceDates::timezone())
            : WorkspaceDates::today();

        return $month->startOfMonth()->locale('pt_BR');
    }

    public function selectedDay(): CarbonImmutable
    {
        $day = $this->parseDate($this->day);

        if ($day && $day->isSameMonth($this->visibleMonth())) {
            return $day;
        }

        $today = WorkspaceDates::today();

        return $today->isSameMonth($this->visibleMonth()) ? $today : $this->visibleMonth();
    }

    protected function parseDate(?string $date): ?CarbonImmutable
    {
        if (! $date || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }

        $parsed = CarbonImmutable::createFromFormat('!Y-m-d', $date, WorkspaceDates::timezone());

        return $parsed && $parsed->format('Y-m-d') === $date ? $parsed->locale('pt_BR') : null;
    }

    /**
     * Grade mensal (segunda a domingo) com os compromissos de cada dia.
     *
     * @return array{label: string, isCurrent: bool, weeks: array<int, array<int, array<string, mixed>>>, selected: array<string, mixed>}
     */
    #[Computed]
    public function calendar(): array
    {
        $today = WorkspaceDates::today();
        $month = $this->visibleMonth();
        $selected = $this->selectedDay();
        $start = $month->startOfWeek(CarbonInterface::MONDAY);
        $end = $month->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY)->startOfDay();

        $events = $this->eventQuery()
            ->where('date', '>=', $start->toDateString())
            ->where('date', '<', $end->addDay()->toDateString())
            ->get()
            ->groupBy(fn (WorkspaceEvent $event) => $event->date->format('Y-m-d'));

        $weeks = [];
        for ($date = $start, $i = 0; $date->lte($end); $date = $date->addDay(), $i++) {
            $key = $date->toDateString();

            $weeks[intdiv($i, 7)][] = [
                'key' => $key,
                'day' => $date->day,
                'label' => ucfirst($date->translatedFormat('l, j \d\e F')),
                'inMonth' => $date->isSameMonth($month),
                'isToday' => $date->equalTo($today),
                'isSelected' => $date->equalTo($selected),
                'events' => $events->get($key, collect()),
            ];
        }

        return [
            'label' => ucfirst($month->translatedFormat('F Y')),
            'isCurrent' => $month->isSameMonth($today),
            'weeks' => $weeks,
            'selected' => [
                'key' => $selected->toDateString(),
                'label' => $selected->equalTo($today)
                    ? 'Hoje, '.$selected->translatedFormat('j \d\e F')
                    : ucfirst($selected->translatedFormat('l, j \d\e F')),
                'events' => $events->get($selected->toDateString(), collect()),
            ],
        ];
    }

    /** @return Collection<int, WorkspaceEvent> */
    #[Computed]
    public function upcoming(): Collection
    {
        return $this->eventQuery()
            ->where('date', '>=', WorkspaceDates::today()->toDateString())
            ->limit(self::UPCOMING_LIMIT)
            ->get();
    }

    protected function eventQuery()
    {
        return WorkspaceEvent::ownedBy(auth()->user())
            ->orderBy('date')
            ->orderByDesc('all_day')
            ->orderBy('starts_at')
            ->orderBy('id');
    }

    // ------------------------------------------------------------------
    // Tarefas
    // ------------------------------------------------------------------

    public function updatedTaskFilter(): void
    {
        if (! array_key_exists($this->taskFilter, self::TASK_FILTERS)) {
            $this->taskFilter = 'pending';
        }
    }

    public function toggleTask(int $id): void
    {
        $task = $this->findTask($id);
        $task->update(['completed_at' => $task->isCompleted() ? null : now()]);

        Notification::make()
            ->title($task->isCompleted() ? 'Tarefa concluída' : 'Tarefa reaberta')
            ->success()
            ->send();
    }

    /** @return Collection<int, WorkspaceTask> */
    #[Computed]
    public function tasks(): Collection
    {
        $query = WorkspaceTask::ownedBy(auth()->user());
        $today = WorkspaceDates::today()->toDateString();

        if ($this->taskFilter === 'done') {
            return $query->whereNotNull('completed_at')
                ->latest('completed_at')
                ->limit(self::DONE_LIMIT)
                ->get();
        }

        if ($this->taskFilter === 'today') {
            // Hoje inclui as atrasadas: são as que pedem ação agora
            $query->whereNotNull('due_date')->whereDate('due_date', '<=', $today);
        }

        return $query->whereNull('completed_at')
            ->get()
            ->sortBy(fn (WorkspaceTask $task) => implode('|', [
                $task->due_date ? '0'.$task->due_date->format('Y-m-d').($task->due_time ?? '99:99') : '1',
                $task->priority->weight(),
                str_pad((string) $task->id, 10, '0', STR_PAD_LEFT),
            ]))
            ->values();
    }

    /** Tarefas pendentes que vencem hoje ou já venceram (contador do atalho no topo do painel) */
    public static function dueTaskCount(): int
    {
        return WorkspaceTask::ownedBy(auth()->user())
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', WorkspaceDates::today()->toDateString())
            ->count();
    }

    /** @return array<string, int> */
    #[Computed]
    public function taskCounts(): array
    {
        $query = WorkspaceTask::ownedBy(auth()->user());

        return [
            'pending' => (clone $query)->whereNull('completed_at')->count(),
            'today' => (clone $query)->whereNull('completed_at')->whereNotNull('due_date')
                ->whereDate('due_date', '<=', WorkspaceDates::today()->toDateString())->count(),
            'done' => (clone $query)->whereNotNull('completed_at')->count(),
        ];
    }

    // ------------------------------------------------------------------
    // Notas
    // ------------------------------------------------------------------

    /** @return Collection<int, WorkspaceNote> */
    #[Computed]
    public function notes(): Collection
    {
        return WorkspaceNote::ownedBy(auth()->user())
            ->latest('updated_at')
            ->latest('id')
            ->get();
    }

    #[Computed]
    public function noteCount(): int
    {
        return WorkspaceNote::ownedBy(auth()->user())->count();
    }

    // ------------------------------------------------------------------
    // Ações com modal (criar e editar usam a mesma ação; o id vem nos argumentos)
    // ------------------------------------------------------------------

    public function taskAction(): Action
    {
        return Action::make('task')
            ->modalHeading(fn (array $arguments) => isset($arguments['id']) ? 'Editar tarefa' : 'Nova tarefa')
            ->modalIcon('heroicon-o-check-circle')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitActionLabel(fn (array $arguments) => isset($arguments['id']) ? 'Salvar' : 'Adicionar tarefa')
            ->fillForm(fn (array $arguments) => isset($arguments['id'])
                ? $this->findTask($arguments['id'])->toFormData()
                : ['priority' => WorkspacePriority::Medium->value])
            ->form(fn (array $arguments) => [
                TextInput::make('title')
                    ->label('Tarefa')
                    ->placeholder('Ex.: Revisar orçamento da obra')
                    ->required()
                    ->maxLength(160)
                    ->autofocus(),
                Grid::make(['default' => 1, 'sm' => 2])->schema([
                    DatePicker::make('due_date')
                        ->label('Prazo')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->closeOnDateSelection()
                        ->prefixIcon('heroicon-m-calendar')
                        ->placeholder('Sem prazo')
                        ->live(),
                    TimePicker::make('due_time')
                        ->label('Horário')
                        ->seconds(false)
                        ->format('H:i')
                        ->prefixIcon('heroicon-m-clock')
                        ->helperText('Opcional.')
                        ->disabled(fn (Get $get) => blank($get('due_date'))),
                ]),
                ToggleButtons::make('priority')
                    ->label('Prioridade')
                    ->options(WorkspacePriority::class)
                    ->enum(WorkspacePriority::class)
                    ->inline()
                    ->required(),
                Toggle::make('is_completed')
                    ->label('Concluída')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->visible(isset($arguments['id'])),
            ])
            ->extraModalFooterActions(fn (array $arguments) => isset($arguments['id'])
                ? [$this->deleteFooterAction('removeTask', 'Excluir tarefa?', fn () => $this->deleteTask($arguments['id']))]
                : [])
            ->action(function (array $data, array $arguments) {
                $values = [
                    'title' => trim($data['title']),
                    'due_date' => $data['due_date'] ?? null,
                    'due_time' => filled($data['due_date'] ?? null) ? ($data['due_time'] ?? null) : null,
                    'priority' => $data['priority'],
                ];

                if (! isset($arguments['id'])) {
                    auth()->user()->workspaceTasks()->create($values);
                    $this->notify('Tarefa adicionada');
                    $this->reveal('tarefas');

                    return;
                }

                $task = $this->findTask($arguments['id']);
                $done = (bool) ($data['is_completed'] ?? false);
                $values['completed_at'] = $done ? ($task->completed_at ?? now()) : null;
                $task->update($values);

                $this->notify('Tarefa atualizada');
            });
    }

    public function eventAction(): Action
    {
        return Action::make('event')
            ->modalHeading(fn (array $arguments) => isset($arguments['id']) ? 'Editar compromisso' : 'Novo compromisso')
            ->modalIcon('heroicon-o-calendar')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitActionLabel(fn (array $arguments) => isset($arguments['id']) ? 'Salvar' : 'Adicionar compromisso')
            ->fillForm(fn (array $arguments) => isset($arguments['id'])
                ? $this->findEvent($arguments['id'])->toFormData()
                : [
                    'date' => $this->parseDate($arguments['date'] ?? null)?->toDateString() ?? $this->selectedDay()->toDateString(),
                    'all_day' => false,
                    'color' => WorkspaceColor::Primary->value,
                ])
            ->form(fn (array $arguments) => [
                TextInput::make('title')
                    ->label('Compromisso')
                    ->placeholder('Ex.: Visita técnica na obra')
                    ->required()
                    ->maxLength(160)
                    ->autofocus(),
                Grid::make(['default' => 1, 'sm' => 2])->schema([
                    DatePicker::make('date')
                        ->label('Data')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->closeOnDateSelection()
                        ->prefixIcon('heroicon-m-calendar')
                        ->required(),
                    Toggle::make('all_day')
                        ->label('Dia inteiro')
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->inline(false)
                        ->live(),
                ]),
                Grid::make(['default' => 2])
                    ->hidden(fn (Get $get) => (bool) $get('all_day'))
                    ->schema([
                        TimePicker::make('starts_at')
                            ->label('Início')
                            ->seconds(false)
                            ->format('H:i')
                            ->prefixIcon('heroicon-m-clock')
                            ->required(),
                        TimePicker::make('ends_at')
                            ->label('Fim')
                            ->seconds(false)
                            ->format('H:i')
                            ->prefixIcon('heroicon-m-clock')
                            ->after('starts_at')
                            ->validationMessages(['after' => 'O fim precisa ser depois do início.']),
                    ]),
                TextInput::make('location')
                    ->label('Local')
                    ->placeholder('Ex.: Obra Residencial - Centro')
                    ->prefixIcon('heroicon-m-map-pin')
                    ->maxLength(160),
                ToggleButtons::make('color')
                    ->label('Cor')
                    ->options(WorkspaceColor::class)
                    ->enum(WorkspaceColor::class)
                    ->inline()
                    ->required(),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3)
                    ->autosize()
                    ->maxLength(1000),
            ])
            ->extraModalFooterActions(fn (array $arguments) => isset($arguments['id'])
                ? [$this->deleteFooterAction('removeEvent', 'Excluir compromisso?', fn () => $this->deleteEvent($arguments['id']))]
                : [])
            ->action(function (array $data, array $arguments) {
                $allDay = (bool) ($data['all_day'] ?? false);
                $values = [
                    'title' => trim($data['title']),
                    'date' => $data['date'],
                    'all_day' => $allDay,
                    'starts_at' => $allDay ? null : ($data['starts_at'] ?? null),
                    'ends_at' => $allDay ? null : ($data['ends_at'] ?? null),
                    'location' => filled($data['location'] ?? null) ? trim($data['location']) : null,
                    'color' => $data['color'],
                    'notes' => filled($data['notes'] ?? null) ? trim($data['notes']) : null,
                ];

                if (isset($arguments['id'])) {
                    $this->findEvent($arguments['id'])->update($values);
                    $this->notify('Compromisso atualizado');
                } else {
                    auth()->user()->workspaceEvents()->create($values);
                    $this->notify('Compromisso adicionado');
                }

                // Mostra no calendário o dia do compromisso salvo
                $this->selectDay($values['date']);
                $this->reveal('agenda', alsoVisibleOn: ['tarefas']);
            });
    }

    public function noteAction(): Action
    {
        return Action::make('note')
            ->modalHeading(fn (array $arguments) => isset($arguments['id']) ? 'Editar nota' : 'Nova nota rápida')
            ->modalIcon('heroicon-o-document-text')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitActionLabel(fn (array $arguments) => isset($arguments['id']) ? 'Salvar' : 'Adicionar nota')
            ->fillForm(fn (array $arguments) => isset($arguments['id'])
                ? $this->findNote($arguments['id'])->toFormData()
                : ['color' => WorkspaceColor::Gray->value])
            ->form([
                TextInput::make('title')
                    ->label('Título')
                    ->placeholder('Ex.: Ideia para campanha de outubro')
                    ->required()
                    ->maxLength(120)
                    ->autofocus(),
                Textarea::make('content')
                    ->label('Conteúdo')
                    ->placeholder('Anote ideias, lembretes ou informações importantes.')
                    ->rows(5)
                    ->autosize()
                    ->maxLength(5000),
                ToggleButtons::make('color')
                    ->label('Cor')
                    ->options(WorkspaceColor::class)
                    ->enum(WorkspaceColor::class)
                    ->inline()
                    ->required(),
            ])
            ->extraModalFooterActions(fn (array $arguments) => isset($arguments['id'])
                ? [$this->deleteFooterAction('removeNote', 'Excluir nota?', fn () => $this->deleteNote($arguments['id']))]
                : [])
            ->action(function (array $data, array $arguments) {
                $values = [
                    'title' => trim($data['title']),
                    'content' => filled($data['content'] ?? null) ? trim($data['content']) : null,
                    'color' => $data['color'],
                ];

                if (isset($arguments['id'])) {
                    $this->findNote($arguments['id'])->update($values);
                    $this->notify('Nota atualizada');
                } else {
                    auth()->user()->workspaceNotes()->create($values);
                    $this->notify('Nota adicionada');
                    $this->reveal('notas');
                }
            });
    }

    public function deleteTaskAction(): Action
    {
        return $this->confirmDeleteAction('deleteTask', 'Excluir tarefa?')
            ->action(fn (array $arguments) => $this->deleteTask($arguments['id'] ?? 0));
    }

    public function deleteEventAction(): Action
    {
        return $this->confirmDeleteAction('deleteEvent', 'Excluir compromisso?')
            ->action(fn (array $arguments) => $this->deleteEvent($arguments['id'] ?? 0));
    }

    public function deleteNoteAction(): Action
    {
        return $this->confirmDeleteAction('deleteNote', 'Excluir nota?')
            ->action(fn (array $arguments) => $this->deleteNote($arguments['id'] ?? 0));
    }

    protected function confirmDeleteAction(string $name, string $heading): Action
    {
        return Action::make($name)
            ->label('Excluir')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading($heading)
            ->modalDescription('Esta ação não pode ser desfeita.')
            ->modalSubmitActionLabel('Excluir');
    }

    /** "Excluir" no rodapé do modal de edição: confirma e fecha o modal de edição junto */
    protected function deleteFooterAction(string $name, string $heading, Closure $delete): Action
    {
        return $this->confirmDeleteAction($name, $heading)
            ->link()
            ->action($delete)
            ->cancelParentActions();
    }

    protected function deleteTask(int|string $id): void
    {
        $this->findTask($id)->delete();
        $this->notify('Tarefa excluída');
    }

    protected function deleteEvent(int|string $id): void
    {
        $this->findEvent($id)->delete();
        $this->notify('Compromisso excluído');
    }

    protected function deleteNote(int|string $id): void
    {
        $this->findNote($id)->delete();
        $this->notify('Nota excluída');
    }

    // ------------------------------------------------------------------
    // Registros do próprio usuário (o de outro usuário dá 404)
    // ------------------------------------------------------------------

    protected function findTask(int|string $id): WorkspaceTask
    {
        return WorkspaceTask::ownedBy(auth()->user())->findOrFail((int) $id);
    }

    protected function findEvent(int|string $id): WorkspaceEvent
    {
        return WorkspaceEvent::ownedBy(auth()->user())->findOrFail((int) $id);
    }

    protected function findNote(int|string $id): WorkspaceNote
    {
        return WorkspaceNote::ownedBy(auth()->user())->findOrFail((int) $id);
    }

    protected function notify(string $title): void
    {
        Notification::make()->title($title)->success()->send();
    }

    /** Ações rodam antes do render na mesma requisição: recalcula tudo depois delas */
    public function rendering(): void
    {
        unset($this->calendar, $this->upcoming, $this->tasks, $this->taskCounts, $this->notes, $this->noteCount);
    }
}
