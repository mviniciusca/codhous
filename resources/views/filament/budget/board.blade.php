<x-filament-panels::page>
    @include('filament.budget.board-styles')

    @php
        $columns = $this->getColumns();
        $statuses = \App\Filament\Resources\BudgetResource::STATUSES;
        $accent = ['pending' => 'warning', 'on going' => 'primary', 'done' => 'success', 'ignored' => 'gray'];
    @endphp

    <div class="bb-toolbar">
        <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass" class="bb-search">
            <x-filament::input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="Buscar por código, cliente, telefone ou cidade"
            />
        </x-filament::input.wrapper>
        <span class="bb-hint">Mostrando orçamentos ativos · até 50 por coluna</span>
    </div>

    <div
        class="bb-board"
        x-data="{ dragging: null, over: null }"
        x-on:dragend.window="dragging = null; over = null"
    >
        @foreach ($columns as $status => $column)
            <section
                @class(['bb-col', 'bb-col--'.$accent[$status]])
                x-bind:class="{ 'bb-col--over': over === @js($status) && dragging?.status !== @js($status) }"
                x-on:dragover.prevent="over = @js($status)"
                x-on:dragleave.self="over = null"
                x-on:drop.prevent="if (dragging && dragging.status !== @js($status)) { $wire.moveBudget(dragging.id, @js($status)) }; dragging = null; over = null"
                aria-label="{{ $column['label'] }}"
                wire:key="col-{{ $status }}"
            >
                <header class="bb-col-head">
                    <div class="bb-col-title">
                        <span class="bb-dot"></span>
                        <h2>{{ $column['label'] }}</h2>
                        <span class="bb-count">{{ $column['count'] }}</span>
                    </div>
                    <span class="bb-sum">R$ {{ number_format($column['total'], 2, ',', '.') }}</span>
                </header>

                <div class="bb-cards">
                    @forelse ($column['cards'] as $card)
                        <article
                            class="bb-card"
                            draggable="true"
                            x-on:dragstart="dragging = { id: {{ $card['id'] }}, status: @js($status) }; $event.dataTransfer.effectAllowed = 'move'"
                            x-bind:class="{ 'bb-card--dragging': dragging?.id === {{ $card['id'] }} }"
                            wire:key="card-{{ $card['id'] }}"
                        >
                            <div class="bb-card-top">
                                <a href="{{ $card['url'] }}" class="bb-code">{{ $card['code'] }}</a>
                                <span class="bb-age">{{ $card['age'] }}</span>

                                <x-filament::dropdown placement="bottom-end" teleport>
                                    <x-slot name="trigger">
                                        <button type="button" class="bb-menu" aria-label="Mover {{ $card['code'] }}">
                                            <x-filament::icon icon="heroicon-m-ellipsis-horizontal" />
                                        </button>
                                    </x-slot>

                                    <x-filament::dropdown.header>Mover para</x-filament::dropdown.header>
                                    <x-filament::dropdown.list>
                                        @foreach ($statuses as $value => $label)
                                            @continue($value === $status)
                                            <x-filament::dropdown.list.item wire:click="moveBudget({{ $card['id'] }}, '{{ $value }}')" x-on:click="close">
                                                {{ $label }}
                                            </x-filament::dropdown.list.item>
                                        @endforeach
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            </div>

                            <a href="{{ $card['url'] }}" class="bb-card-link">
                                <strong class="bb-customer">{{ $card['customer'] }}</strong>
                                @if ($card['place'])
                                    <span class="bb-place">{{ $card['place'] }}</span>
                                @endif
                                @if ($card['items'])
                                    <span class="bb-items">{{ $card['items'] }}</span>
                                @endif
                            </a>

                            <div class="bb-card-foot">
                                <span class="bb-total">R$ {{ number_format($card['total'], 2, ',', '.') }}</span>

                                <span class="bb-flags">
                                    @if ($card['stale'])
                                        <span class="bb-flag bb-flag--warn" title="Sem movimentação há mais de {{ \App\Filament\Resources\BudgetResource\Pages\BudgetBoard::STALE_AFTER_DAYS }} dias">
                                            <x-filament::icon icon="heroicon-m-clock" /> Parado
                                        </span>
                                    @endif
                                    @if ($card['delivery'])
                                        <span @class(['bb-flag', 'bb-flag--warn' => $card['delivery_soon']]) title="Entrega desejada">
                                            <x-filament::icon icon="heroicon-m-truck" /> {{ $card['delivery'] }}
                                        </span>
                                    @endif
                                    @if ($card['whatsapp'])
                                        <span class="bb-flag bb-flag--ok" title="Enviado pelo WhatsApp"><x-filament::icon icon="heroicon-m-chat-bubble-left-right" /></span>
                                    @endif
                                    @if ($card['email'])
                                        <span class="bb-flag bb-flag--ok" title="Enviado por e-mail"><x-filament::icon icon="heroicon-m-envelope" /></span>
                                    @endif
                                    @if ($card['from_site'])
                                        <span class="bb-flag" title="Pedido do site"><x-filament::icon icon="heroicon-m-globe-alt" /></span>
                                    @endif
                                </span>
                            </div>
                        </article>
                    @empty
                        <p class="bb-empty">{{ filled($search) ? 'Nada encontrado.' : 'Nenhum orçamento.' }}</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
