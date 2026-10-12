<x-filament-panels::page class="fi-dashboard-page">
    @include('filament.dashboard.styles')

    @php
        $overview = $this->overview;
        $kpis = $overview->kpis();
        $agenda = $this->agenda;
        $attention = $overview->attention();
        $recent = $overview->recentBudgets();
        $topProducts = $overview->topProducts();
    @endphp

    <div class="cx" wire:poll.visible.60s>
        {{-- Indicadores: uma faixa única, cada segmento leva à lista já filtrada --}}
        <section class="cx-kpis" aria-label="Indicadores comerciais">
            <x-dashboard.kpi
                label="Novos pedidos"
                :value="$kpis['new']['value']"
                :note="$kpis['new']['note']"
                :tone="$kpis['new']['tone']"
                :url="$kpis['new']['url']"
            >
                @php $peak = max(1, max($kpis['new']['trend'])); @endphp
                <span class="cx-spark" role="img" aria-label="Pedidos recebidos por dia nos últimos 14 dias: {{ implode(', ', $kpis['new']['trend']) }}">
                    @foreach ($kpis['new']['trend'] as $value)
                        <span @class(['cx-spark-bar', 'is-empty' => $value === 0, 'is-last' => $loop->last]) style="height: {{ $value === 0 ? 8 : max(16, round($value / $peak * 100)) }}%"></span>
                    @endforeach
                </span>
            </x-dashboard.kpi>

            <x-dashboard.kpi
                label="Em negociação"
                :value="$kpis['negotiation']['value']"
                :note="$kpis['negotiation']['note']"
                :tone="$kpis['negotiation']['tone']"
                :url="$kpis['negotiation']['url']"
            />

            <x-dashboard.kpi
                :label="$kpis['closed']['label']"
                :value="$kpis['closed']['value']"
                :note="$kpis['closed']['note']"
                :tone="$kpis['closed']['tone']"
                :url="$kpis['closed']['url']"
            >
                @if ($kpis['closed']['delta'] !== null)
                    @php $delta = $kpis['closed']['delta']; @endphp
                    <span @class(['cx-delta', 'is-up' => $delta > 0, 'is-down' => $delta < 0])>
                        <x-filament::icon
                            :icon="$delta > 0 ? 'heroicon-m-arrow-trending-up' : ($delta < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus')"
                            class="cx-icon-sm"
                            aria-hidden="true"
                        />
                        {{ $delta > 0 ? '+' : '' }}{{ $delta }}%
                        <span class="cx-delta-label">{{ $kpis['closed']['deltaLabel'] }}</span>
                    </span>
                @endif
            </x-dashboard.kpi>

            <x-dashboard.kpi
                label="Conversão · {{ \App\Services\DashboardOverview::WINDOW_DAYS }} dias"
                :value="$kpis['conversion']['value']"
                :note="$kpis['conversion']['note']"
                :tone="$kpis['conversion']['tone']"
                :url="$kpis['conversion']['url']"
            >
                <span class="cx-meter" role="meter" aria-label="Taxa de conversão" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $kpis['conversion']['rate'] ?? 0 }}">
                    <span style="width: {{ $kpis['conversion']['rate'] ?? 0 }}%"></span>
                </span>
            </x-dashboard.kpi>
        </section>

        <div class="cx-row">
            {{-- Agenda de entregas --}}
            <x-dashboard.panel
                title="Agenda de entregas"
                description="Datas pedidas pelos clientes nos próximos {{ \App\Services\DashboardOverview::AGENDA_DAYS }} dias"
                icon="heroicon-o-truck"
                :href="$agenda['listUrl']"
                link-label="Ver na lista"
            >
                <div
                    x-data="{
                        day: @js($agenda['today']['key']),
                        days: @js(collect($agenda['days'])->pluck('key')),
                        move(step) {
                            const next = this.days[(this.days.indexOf(this.day) + step + this.days.length) % this.days.length];
                            this.day = next;
                            this.$nextTick(() => this.$refs['tab-' + next]?.focus());
                        },
                    }"
                >
                    <div class="cx-week" role="tablist" aria-label="Dias da agenda" x-on:keydown.right.prevent="move(1)" x-on:keydown.left.prevent="move(-1)">
                        @foreach ($agenda['days'] as $day)
                            <button
                                type="button"
                                role="tab"
                                id="cx-tab-{{ $day['key'] }}"
                                aria-controls="cx-day-{{ $day['key'] }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                tabindex="{{ $loop->first ? 0 : -1 }}"
                                x-ref="tab-{{ $day['key'] }}"
                                x-on:click="day = @js($day['key'])"
                                x-bind:aria-selected="day === @js($day['key'])"
                                x-bind:tabindex="day === @js($day['key']) ? 0 : -1"
                                x-bind:class="{ 'is-active': day === @js($day['key']) }"
                                @class(['cx-week-day', 'is-active' => $loop->first, 'is-today' => $loop->first])
                                wire:key="agenda-tab-{{ $day['key'] }}"
                                {{-- Estado da aba fica com o Alpine; o polling só atualiza o conteúdo --}}
                                wire:ignore.self
                            >
                                <span class="cx-sr">{{ $day['long'] }}</span>
                                <span class="cx-week-name" aria-hidden="true">{{ $day['weekday'] }}</span>
                                <span class="cx-week-num" aria-hidden="true">{{ $day['day'] }}</span>
                                <span @class(['cx-week-load', 'is-idle' => $day['count'] === 0]) aria-hidden="true"><span style="width: {{ $day['load'] }}%"></span></span>
                                <span @class(['cx-week-meta', 'is-idle' => $day['count'] === 0])>
                                    @if ($day['count'] === 0)
                                        <span aria-hidden="true">—</span><span class="cx-sr">sem entregas</span>
                                    @else
                                        {{ $day['count'] }} · {{ $day['volume'] > 0 ? $day['volumeLabel'] : ($day['count'] === 1 ? 'entrega' : 'entregas') }}
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </div>

                    @foreach ($agenda['days'] as $day)
                        <div
                            role="tabpanel"
                            id="cx-day-{{ $day['key'] }}"
                            aria-labelledby="cx-tab-{{ $day['key'] }}"
                            x-show="day === @js($day['key'])"
                            @if (! $loop->first) style="display: none" @endif
                            class="cx-day"
                            wire:key="agenda-{{ $day['key'] }}"
                            wire:ignore.self
                        >
                            <div class="cx-day-head">
                                <strong>{{ $day['long'] }}</strong>
                                @if ($day['count'] > 0)
                                    <span>
                                        {{ $day['count'] }} {{ $day['count'] === 1 ? 'entrega' : 'entregas' }}
                                        @if ($day['volume'] > 0) · {{ $day['volumeLabel'] }} @endif
                                        · {{ $day['confirmed'] }} {{ $day['confirmed'] === 1 ? 'fechada' : 'fechadas' }}
                                    </span>
                                @endif
                            </div>

                            @forelse ($day['entries'] as $entry)
                                <a href="{{ $entry['url'] }}" class="cx-delivery" wire:key="delivery-{{ $entry['id'] }}">
                                    <span @class(['cx-period', 'is-'.($entry['periodKey'] ?? 'none')])>{{ $entry['period'] }}</span>

                                    <span class="cx-delivery-main">
                                        <span class="cx-delivery-customer">{{ $entry['customer'] }}</span>
                                        <span class="cx-delivery-sub">
                                            <span class="cx-code">{{ $entry['code'] }}</span>
                                            @if ($entry['place'])
                                                <span class="cx-sep" aria-hidden="true"></span>
                                                <x-filament::icon icon="heroicon-m-map-pin" class="cx-icon-xs" aria-hidden="true" />
                                                <span class="cx-place">{{ $entry['place'] }}</span>
                                            @endif
                                        </span>
                                    </span>

                                    <span class="cx-delivery-items">
                                        <span>{{ $entry['items'] ?? 'Sem itens' }}</span>
                                        @if ($entry['pump'])
                                            <span class="cx-tag">{{ $entry['pump'] }}</span>
                                        @endif
                                    </span>

                                    <span class="cx-delivery-volume">{{ $entry['volumeLabel'] ?? '—' }}</span>

                                    <x-dashboard.status :status="$entry['status']" />

                                    <x-filament::icon icon="heroicon-m-chevron-right" class="cx-row-go" aria-hidden="true" />
                                </a>
                            @empty
                                <x-dashboard.empty
                                    icon="heroicon-o-calendar"
                                    title="Nenhuma entrega para este dia"
                                    description="Pedidos com data de entrega aparecem aqui automaticamente."
                                />
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </x-dashboard.panel>

            {{-- Pendências --}}
            <x-dashboard.panel
                title="Atenção"
                description="O que precisa de ação agora"
                icon="heroicon-o-bell-alert"
                :count="count($attention) ?: null"
            >
                @if (count($attention))
                    <ul class="cx-alerts" role="list">
                        @foreach ($attention as $item)
                            <li>
                                <a href="{{ $item['url'] }}" @class(['cx-alert', 'cx-tone-'.$item['tone']])>
                                    <span class="cx-alert-icon" aria-hidden="true">
                                        <x-filament::icon :icon="$item['icon']" class="cx-icon" />
                                    </span>
                                    <span class="cx-alert-text">
                                        <span class="cx-alert-title"><b>{{ $item['count'] }}</b> {{ $item['title'] }}</span>
                                        <span class="cx-alert-note">{{ $item['note'] }}</span>
                                    </span>
                                    <x-filament::icon icon="heroicon-m-chevron-right" class="cx-row-go" aria-hidden="true" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-dashboard.empty
                        icon="heroicon-o-check-circle"
                        tone="success"
                        title="Tudo em dia"
                        description="Nenhum pedido parado, entrega vencida ou mensagem sem leitura."
                    />
                @endif
            </x-dashboard.panel>
        </div>

        <div class="cx-row">
            {{-- Orçamentos recentes --}}
            <x-dashboard.panel
                title="Orçamentos recentes"
                description="Últimos pedidos recebidos pelo site e criados pela equipe"
                icon="heroicon-o-document-text"
                :href="\App\Filament\Resources\BudgetResource::getUrl('index', ['activeTab' => 'todos'])"
            >
                @if (count($recent))
                    <div class="cx-table-wrap">
                        <table class="cx-table">
                            <thead>
                                <tr>
                                    <th scope="col">Orçamento</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col" class="cx-hide-lg">Itens</th>
                                    <th scope="col" class="cx-hide-sm">Etapa</th>
                                    <th scope="col" class="cx-num">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent as $budget)
                                    <tr wire:key="recent-{{ $budget['id'] }}">
                                        <td>
                                            <a href="{{ $budget['url'] }}" class="cx-code-link">{{ $budget['code'] }}</a>
                                            <span class="cx-cell-sub" title="{{ $budget['date'] }}">{{ $budget['ago'] }} · {{ $budget['fromSite'] ? 'site' : 'painel' }}</span>
                                        </td>
                                        <td class="cx-cell-clip">
                                            <span class="cx-cell-main">{{ $budget['customer'] }}</span>
                                            @if ($budget['place'])
                                                <span class="cx-cell-sub">{{ $budget['place'] }}</span>
                                            @endif
                                        </td>
                                        <td class="cx-hide-lg cx-cell-clip"><span class="cx-cell-sub">{{ $budget['items'] ?? '—' }}</span></td>
                                        <td class="cx-hide-sm"><x-dashboard.status :status="$budget['status']" /></td>
                                        <td class="cx-num"><span class="cx-cell-main">{{ $budget['total'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty
                        icon="heroicon-o-document-plus"
                        title="Nenhum orçamento ainda"
                        description="Pedidos feitos pelo site e orçamentos criados aqui aparecem nesta lista."
                    >
                        <x-filament::button tag="a" size="sm" icon="heroicon-m-plus" :href="\App\Filament\Resources\BudgetResource::getUrl('create')">
                            Novo orçamento
                        </x-filament::button>
                    </x-dashboard.empty>
                @endif
            </x-dashboard.panel>

            {{-- Mix de produtos --}}
            <x-dashboard.panel
                title="Mais vendidos"
                description="Faturamento por produto em orçamentos fechados · {{ \App\Services\DashboardOverview::WINDOW_DAYS }} dias"
                icon="heroicon-o-cube"
            >
                @if (count($topProducts))
                    <ol class="cx-rank" role="list">
                        @foreach ($topProducts as $product)
                            <li class="cx-rank-row">
                                <span class="cx-rank-head">
                                    <span class="cx-rank-name">{{ $product['name'] }}</span>
                                    <span class="cx-rank-value">{{ $product['revenueLabel'] }}</span>
                                </span>
                                <span class="cx-rank-bar" aria-hidden="true"><span style="width: {{ $product['share'] }}%"></span></span>
                                <span class="cx-rank-sub">{{ $product['quantityLabel'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <x-dashboard.empty
                        icon="heroicon-o-chart-bar"
                        title="Sem vendas no período"
                        description="O ranking aparece quando houver orçamentos fechados."
                    />
                @endif
            </x-dashboard.panel>
        </div>
    </div>
</x-filament-panels::page>
