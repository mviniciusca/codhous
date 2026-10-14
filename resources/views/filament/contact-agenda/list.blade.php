<x-filament-panels::page class="fi-contact-agenda-page">
    @include('filament.contact-agenda.styles')

    @php
        $calendar = $this->calendar;
        $upcoming = $this->upcomingInteractions;
        $categoryCounts = $this->categoryCounts;
    @endphp

    <div class="ca-page cx-ui">
        <div class="ca-layout">
            {{-- Coluna Principal: Tabela Nativa do Filament (com busca, filtros, ordenação, abas e paginação completa) --}}
            <main class="ca-main">
                {{ $this->table }}
            </main>

            {{-- Coluna Lateral: Calendário de Contatos, Próximos Contatos e Categorias --}}
            <aside class="ca-sidebar">
                {{-- Widget 1: Calendário de Contatos --}}
                <div class="ca-widget">
                    <div class="ca-widget-head">
                        <span class="ca-widget-title">Calendário de Contatos</span>
                    </div>

                    <div class="ca-cal-header">
                        <span class="ca-cal-month-title">{{ $calendar['label'] }}</span>
                        <div class="ca-cal-nav">
                            <button
                                type="button"
                                wire:click="previousMonth"
                                class="ca-cal-btn"
                                aria-label="Mês anterior"
                            >
                                <x-filament::icon icon="heroicon-m-chevron-left" class="w-3.5 h-3.5" />
                            </button>
                            <button
                                type="button"
                                wire:click="nextMonth"
                                class="ca-cal-btn"
                                aria-label="Próximo mês"
                            >
                                <x-filament::icon icon="heroicon-m-chevron-right" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <div class="ca-cal-grid">
                        @foreach (['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $wd)
                            <div class="ca-cal-weekday">{{ $wd }}</div>
                        @endforeach

                        @foreach ($calendar['weeks'] as $week)
                            @foreach ($week as $day)
                                <button
                                    type="button"
                                    wire:key="cal-day-{{ $day['fullDate'] }}"
                                    wire:click="selectDay('{{ $day['fullDate'] }}')"
                                    @class([
                                        'ca-cal-day',
                                        'is-out' => ! $day['inMonth'],
                                        'is-today' => $day['isToday'],
                                        'is-selected' => $day['isSelected'],
                                    ])
                                >
                                    <span>{{ $day['day'] }}</span>

                                    @if (! empty($day['types']))
                                        <div class="ca-cal-day-dots">
                                            @foreach ($day['types'] as $type)
                                                @php
                                                    $dotColor = match($type) {
                                                        'meeting' => '#3b82f6',
                                                        'call' => '#10b981',
                                                        'email' => '#f59e0b',
                                                        default => '#a855f7',
                                                    };
                                                @endphp
                                                <span class="ca-cal-dot" style="background-color: {{ $dotColor }};"></span>
                                            @endforeach
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        @endforeach
                    </div>

                    <div class="ca-cal-legend">
                        <div class="ca-legend-items">
                            <span class="ca-legend-item">
                                <span class="ca-legend-dot" style="background-color: #3b82f6;"></span>
                                Reunião
                            </span>
                            <span class="ca-legend-item">
                                <span class="ca-legend-dot" style="background-color: #10b981;"></span>
                                Ligação
                            </span>
                            <span class="ca-legend-item">
                                <span class="ca-legend-dot" style="background-color: #f59e0b;"></span>
                                E-mail
                            </span>
                            <span class="ca-legend-item">
                                <span class="ca-legend-dot" style="background-color: #a855f7;"></span>
                                Outro
                            </span>
                        </div>
                        <button
                            type="button"
                            wire:click="goToToday"
                            class="ca-btn-today"
                        >
                            Hoje
                        </button>
                    </div>
                </div>

                {{-- Widget 2: Próximos Contatos --}}
                <div class="ca-widget">
                    <div class="ca-widget-head">
                        <span class="ca-widget-title">Próximos Contatos</span>
                        <a
                            href="{{ \App\Filament\Resources\ContactAgendaResource::getUrl('index') }}"
                            class="ca-widget-link"
                        >
                            Ver todos →
                        </a>
                    </div>

                    <div class="ca-upcoming-list">
                        @forelse ($upcoming as $item)
                            <div class="ca-upcoming-item" wire:key="upcoming-{{ $item->id }}">
                                <div class="ca-upcoming-time-box">
                                    <span class="ca-upcoming-time">{{ $item->scheduled_at->format('H:i') }}</span>
                                    @php
                                        $typeIcon = match($item->type) {
                                            'meeting' => 'heroicon-m-users',
                                            'call' => 'heroicon-m-phone',
                                            'email' => 'heroicon-m-envelope',
                                            default => 'heroicon-m-calendar',
                                        };
                                        $typeColor = match($item->type) {
                                            'meeting' => 'text-blue-400',
                                            'call' => 'text-emerald-400',
                                            'email' => 'text-amber-400',
                                            default => 'text-purple-400',
                                        };
                                    @endphp
                                    <x-filament::icon :icon="$typeIcon" class="ca-upcoming-type-icon {{ $typeColor }}" />
                                </div>
                                <div class="ca-upcoming-content">
                                    <span class="ca-upcoming-title">{{ $item->title }}</span>
                                    <span class="ca-upcoming-contact">{{ $item->contact?->name ?? 'Sem contato' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-gray-500">
                                Nenhum contato agendado para os próximos dias.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Widget 3: Categorias (ativa as abas nativas ao clicar) --}}
                <div class="ca-widget">
                    <div class="ca-widget-head">
                        <span class="ca-widget-title">Categorias</span>
                        <button
                            type="button"
                            wire:click="selectCategoryTab('todos')"
                            class="ca-widget-link"
                        >
                            Ver todas →
                        </button>
                    </div>

                    <div class="ca-cat-list">
                        <div
                            wire:click="selectCategoryTab('clientes')"
                            @class(['ca-cat-row', 'is-active' => $activeTab === 'clientes'])
                        >
                            <div class="ca-cat-left">
                                <div class="ca-cat-icon-badge" style="background: rgba(var(--success-500), 0.15); color: rgb(var(--success-400));">
                                    <x-filament::icon icon="heroicon-m-user" class="w-4 h-4" />
                                </div>
                                <span class="ca-cat-name">Cliente</span>
                            </div>
                            <div class="ca-cat-right">
                                <span>{{ $categoryCounts['client'] }}</span>
                                <span class="ca-cat-arrow">›</span>
                            </div>
                        </div>

                        <div
                            wire:click="selectCategoryTab('leads')"
                            @class(['ca-cat-row', 'is-active' => $activeTab === 'leads'])
                        >
                            <div class="ca-cat-left">
                                <div class="ca-cat-icon-badge" style="background: rgba(var(--primary-500), 0.15); color: rgb(var(--primary-400));">
                                    <x-filament::icon icon="heroicon-m-bolt" class="w-4 h-4" />
                                </div>
                                <span class="ca-cat-name">Lead</span>
                            </div>
                            <div class="ca-cat-right">
                                <span>{{ $categoryCounts['lead'] }}</span>
                                <span class="ca-cat-arrow">›</span>
                            </div>
                        </div>

                        <div
                            wire:click="selectCategoryTab('fornecedores')"
                            @class(['ca-cat-row', 'is-active' => $activeTab === 'fornecedores'])
                        >
                            <div class="ca-cat-left">
                                <div class="ca-cat-icon-badge" style="background: rgba(var(--purple-500), 0.15); color: rgb(var(--purple-400));">
                                    <x-filament::icon icon="heroicon-m-truck" class="w-4 h-4" />
                                </div>
                                <span class="ca-cat-name">Fornecedor</span>
                            </div>
                            <div class="ca-cat-right">
                                <span>{{ $categoryCounts['supplier'] }}</span>
                                <span class="ca-cat-arrow">›</span>
                            </div>
                        </div>

                        <div
                            wire:click="selectCategoryTab('parceiros')"
                            @class(['ca-cat-row', 'is-active' => $activeTab === 'parceiros'])
                        >
                            <div class="ca-cat-left">
                                <div class="ca-cat-icon-badge" style="background: rgba(var(--warning-500), 0.15); color: rgb(var(--warning-400));">
                                    <x-filament::icon icon="heroicon-m-user-group" class="w-4 h-4" />
                                </div>
                                <span class="ca-cat-name">Parceiro</span>
                            </div>
                            <div class="ca-cat-right">
                                <span>{{ $categoryCounts['partner'] }}</span>
                                <span class="ca-cat-arrow">›</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
