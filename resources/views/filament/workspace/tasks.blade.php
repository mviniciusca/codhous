{{-- Aba Tarefas: a lista ocupa a área principal; os próximos compromissos ficam ao lado --}}
@php
    $tasks = $this->tasks;
    $counts = $this->taskCounts;
    $upcoming = $this->upcoming;
@endphp

<div class="ws-split">
    <x-dashboard.panel
        title="Minhas tarefas"
        description="Organize suas atividades e acompanhe seus prazos."
        icon="heroicon-o-check-circle"
        :count="$counts['pending'] ?: null"
    >
        <x-slot name="actions">
            <x-filament::button size="sm" icon="heroicon-m-plus" wire:click="mountAction('task')">
                Adicionar tarefa
            </x-filament::button>
        </x-slot>

        <x-filament::tabs contained label="Filtrar tarefas" class="ws-tabs">
            @foreach (\App\Filament\Pages\Workspace::TASK_FILTERS as $filter => $label)
                <x-filament::tabs.item
                    :active="$taskFilter === $filter"
                    :badge="$counts[$filter] ?: null"
                    wire:click="$set('taskFilter', '{{ $filter }}')"
                >
                    {{ $label }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        @if ($tasks->isNotEmpty())
            <ul class="ws-tasks" role="list">
                @foreach ($tasks as $task)
                    @php
                        $done = $task->isCompleted();
                        $overdue = $task->isOverdue($today);
                        $due = $task->dueLabel($today);
                    @endphp
                    <li @class(['ws-task', 'is-done' => $done]) wire:key="task-{{ $task->id }}">
                        <button
                            type="button"
                            role="checkbox"
                            aria-checked="{{ $done ? 'true' : 'false' }}"
                            aria-label="{{ $done ? 'Reabrir' : 'Concluir' }}: {{ $task->title }}"
                            class="ws-check"
                            wire:click="toggleTask({{ $task->id }})"
                            wire:loading.attr="disabled"
                            wire:target="toggleTask({{ $task->id }})"
                        >
                            <x-filament::icon icon="heroicon-m-check" class="ws-check-icon" />
                        </button>

                        <button type="button" class="ws-task-title" wire:click="mountAction('task', { id: {{ $task->id }} })" title="{{ $task->title }}">
                            {{ $task->title }}
                        </button>

                        <span class="ws-task-meta">
                            @if ($done)
                                <span class="cx-badge cx-tone-success ws-task-badge">
                                    <x-filament::icon icon="heroicon-m-check-circle" class="cx-icon-xs" />
                                    Concluída
                                </span>
                            @else
                                <span class="cx-badge cx-tone-{{ $task->priority->getColor() }} ws-task-badge">
                                    <x-filament::icon :icon="$task->priority->getIcon()" class="cx-icon-xs" />
                                    {{ $task->priority->getLabel() }}
                                </span>
                            @endif

                            <span @class(['ws-due', 'is-overdue' => $overdue, 'is-empty' => ! $due])>
                                @if ($due)
                                    <x-filament::icon :icon="$overdue ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-calendar'" class="cx-icon-xs" />
                                    {{ $due }}
                                    @if ($overdue)<span class="cx-sr">(atrasada)</span>@endif
                                @else
                                    Sem prazo
                                @endif
                            </span>
                        </span>

                        <x-workspace.item-menu action="task" delete-action="deleteTask" :arguments="['id' => $task->id]" :label="$task->title" />
                    </li>
                @endforeach
            </ul>
        @else
            @switch($taskFilter)
                @case('done')
                    <x-dashboard.empty icon="heroicon-o-check-badge" title="Nenhuma tarefa concluída" description="As tarefas que você concluir aparecem aqui." />
                    @break
                @case('today')
                    <x-dashboard.empty icon="heroicon-o-sun" tone="success" title="Nada para hoje" description="Nenhuma tarefa vence hoje ou está atrasada." />
                    @break
                @default
                    <x-dashboard.empty icon="heroicon-o-clipboard-document-check" title="Nenhuma tarefa pendente" description="Anote o que precisa ser feito e acompanhe os prazos.">
                        <x-filament::button size="sm" icon="heroicon-m-plus" wire:click="mountAction('task')">
                            Adicionar tarefa
                        </x-filament::button>
                    </x-dashboard.empty>
            @endswitch
        @endif
    </x-dashboard.panel>

    <x-dashboard.panel
        title="Próximos compromissos"
        icon="heroicon-o-clock"
        :href="\App\Filament\Pages\Workspace::getUrl(['aba' => 'agenda'])"
        link-label="Ver agenda"
    >
        @if ($upcoming->isNotEmpty())
            <ul class="ws-events" role="list">
                @foreach ($upcoming as $event)
                    <li class="ws-event cx-tone-{{ $event->color->value }}" wire:key="upcoming-{{ $event->id }}">
                        <span class="ws-date-box" aria-hidden="true">
                            <b>{{ $event->date->format('d') }}</b>
                            <small>{{ \Illuminate\Support\Str::upper(rtrim($event->date->locale('pt_BR')->translatedFormat('M'), '.')) }}</small>
                        </span>
                        <button type="button" class="ws-event-main" wire:click="mountAction('event', { id: {{ $event->id }} })">
                            <span class="ws-event-title">{{ $event->title }}</span>
                            <span class="ws-event-meta">
                                <span class="cx-sr">{{ \App\Support\WorkspaceDates::relativeDay($event->date, $today) }},</span>
                                <span>{{ $event->timeLabel() }}</span>
                                @if ($event->location)
                                    <span class="cx-sep" aria-hidden="true"></span>
                                    <span class="ws-event-place">{{ $event->location }}</span>
                                @endif
                            </span>
                        </button>
                        <x-workspace.item-menu action="event" delete-action="deleteEvent" :arguments="['id' => $event->id]" :label="$event->title" />
                    </li>
                @endforeach
            </ul>
        @else
            <x-dashboard.empty icon="heroicon-o-calendar" title="Agenda livre" description="Seus próximos compromissos aparecem aqui.">
                <x-filament::button size="sm" color="gray" icon="heroicon-m-plus" wire:click="mountAction('event')">
                    Novo compromisso
                </x-filament::button>
            </x-dashboard.empty>
        @endif
    </x-dashboard.panel>
</div>
