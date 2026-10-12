{{-- Aba Agenda: calendário mensal na área principal e o dia selecionado ao lado --}}
@php
    $calendar = $this->calendar;
    $selected = $calendar['selected'];
@endphp

<div class="ws-split">
    <x-dashboard.panel title="Calendário" icon="heroicon-o-calendar-days" class="ws-calendar">
        <x-slot name="actions">
            <div class="ws-cal-nav">
                <x-filament::button size="sm" color="gray" wire:click="goToToday" :disabled="$calendar['isCurrent'] && $selected['key'] === $today->toDateString()">
                    Hoje
                </x-filament::button>
                <x-filament::icon-button icon="heroicon-m-chevron-left" color="gray" label="Mês anterior" wire:click="previousMonth" />
                <span class="ws-cal-month" aria-live="polite">{{ $calendar['label'] }}</span>
                <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" label="Próximo mês" wire:click="nextMonth" />
            </div>
        </x-slot>

        <div class="ws-cal">
            <div class="ws-cal-weekdays" aria-hidden="true">
                @foreach (['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $weekday)
                    <span>{{ $weekday }}</span>
                @endforeach
            </div>

            <div class="ws-cal-grid">
                @foreach ($calendar['weeks'] as $week)
                    @foreach ($week as $day)
                        @php $count = $day['events']->count(); @endphp
                        <button
                            type="button"
                            wire:key="day-{{ $day['key'] }}"
                            wire:click="selectDay('{{ $day['key'] }}')"
                            aria-pressed="{{ $day['isSelected'] ? 'true' : 'false' }}"
                            aria-label="{{ $day['label'] }}{{ $count ? ', '.$count.' '.($count === 1 ? 'compromisso' : 'compromissos') : '' }}"
                            @class([
                                'ws-day',
                                'is-out' => ! $day['inMonth'],
                                'is-today' => $day['isToday'],
                                'is-selected' => $day['isSelected'],
                            ])
                        >
                            <span class="ws-day-num">{{ $day['day'] }}</span>

                            @if ($count)
                                <span class="ws-day-events" aria-hidden="true">
                                    @foreach ($day['events']->take($count > 2 ? 1 : 2) as $event)
                                        <span class="ws-day-event cx-tone-{{ $event->color->value }}">
                                            <span class="cx-dot"></span>
                                            <span class="ws-day-event-title">{{ $event->title }}</span>
                                        </span>
                                    @endforeach
                                    @if ($count > 2)
                                        <span class="ws-day-more">+{{ $count - 1 }} mais</span>
                                    @endif
                                </span>
                                {{-- Telas pequenas: só os pontos --}}
                                <span class="ws-day-dots" aria-hidden="true">
                                    @foreach ($day['events']->take(3) as $event)
                                        <span class="cx-dot cx-tone-{{ $event->color->value }}"></span>
                                    @endforeach
                                </span>
                            @endif
                        </button>
                    @endforeach
                @endforeach
            </div>
        </div>
    </x-dashboard.panel>

    {{-- Dia selecionado --}}
    <x-dashboard.panel :title="$selected['label']" icon="heroicon-o-clock" :count="$selected['events']->count() ?: null">
        <x-slot name="actions">
            <x-filament::button size="sm" icon="heroicon-m-plus" wire:click="mountAction('event', { date: '{{ $selected['key'] }}' })">
                Novo
            </x-filament::button>
        </x-slot>

        @if ($selected['events']->isNotEmpty())
            <ul class="ws-events" role="list">
                @foreach ($selected['events'] as $event)
                    <li class="ws-event cx-tone-{{ $event->color->value }}" wire:key="day-event-{{ $event->id }}">
                        <span class="ws-time-box">
                            @if ($event->all_day || ! $event->starts_at)
                                <b>Dia</b><small>inteiro</small>
                            @else
                                <b>{{ $event->starts_at }}</b>
                                @if ($event->ends_at)<small>{{ $event->ends_at }}</small>@endif
                            @endif
                        </span>
                        <button type="button" class="ws-event-main" wire:click="mountAction('event', { id: {{ $event->id }} })">
                            <span class="ws-event-title">
                                <span class="cx-dot" aria-hidden="true"></span>
                                {{ $event->title }}
                            </span>
                            @if ($event->location || $event->notes)
                                <span class="ws-event-meta">
                                    @if ($event->location)
                                        <x-filament::icon icon="heroicon-m-map-pin" class="cx-icon-xs" />
                                        <span class="ws-event-place">{{ $event->location }}</span>
                                    @else
                                        <span class="ws-event-place">{{ $event->notes }}</span>
                                    @endif
                                </span>
                            @endif
                        </button>
                        <x-workspace.item-menu action="event" delete-action="deleteEvent" :arguments="['id' => $event->id]" :label="$event->title" />
                    </li>
                @endforeach
            </ul>
        @else
            <x-dashboard.empty icon="heroicon-o-calendar" title="Nenhum compromisso neste dia" description="Escolha outro dia no calendário ou cadastre um compromisso.">
                <x-filament::button size="sm" color="gray" icon="heroicon-m-plus" wire:click="mountAction('event', { date: '{{ $selected['key'] }}' })">
                    Novo compromisso
                </x-filament::button>
            </x-dashboard.empty>
        @endif
    </x-dashboard.panel>
</div>
