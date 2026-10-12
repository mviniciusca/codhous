{{--
    Alertas e notificações: indicadores, filtros e cards com miniatura de cada alerta. Prefixo al-.
    Barra de filtros, segmentos, interruptor e faixas reaproveitam as classes cs- da tela de Seções.
--}}
@php
    use App\Filament\Resources\AlertResource;
    use App\Filament\Resources\AlertResource\Pages\ListAlerts;
    use App\Models\Alert;

    $isList = $display === 'lista';
    $loadingTargets = 'search,type,style,status,sort,display,clearFilters,moveAlert';

    $positionText = [
        Alert::POSITION_TOP => ['heroicon-m-arrow-up', 'Exibido no topo'],
        Alert::POSITION_BOTTOM => ['heroicon-m-arrow-down', 'Exibido no rodapé'],
        Alert::POSITION_TOP_LEFT => ['heroicon-m-arrow-up-left', 'Canto superior esquerdo'],
        Alert::POSITION_TOP_RIGHT => ['heroicon-m-arrow-up-right', 'Canto superior direito'],
        Alert::POSITION_BOTTOM_LEFT => ['heroicon-m-arrow-down-left', 'Canto inferior esquerdo'],
        Alert::POSITION_BOTTOM_RIGHT => ['heroicon-m-arrow-down-right', 'Canto inferior direito'],
        Alert::POSITION_CENTER => ['heroicon-m-arrows-pointing-in', 'Centro da tela'],
    ];

    $kpis = [
        ['label' => 'Total de alertas', 'icon' => 'heroicon-o-bell', 'tone' => 'primary', 'value' => $stats['total'],
            'note' => 'Todos os cadastrados', 'click' => 'clearFilters', 'active' => ! $this->hasFilters()],
        ['label' => 'Ativos', 'icon' => 'heroicon-o-check-circle', 'tone' => 'success', 'value' => $stats[Alert::STATUS_ACTIVE],
            'note' => 'Aparecendo no site agora', 'click' => "\$set('status', 'active')", 'active' => $status === Alert::STATUS_ACTIVE],
        ['label' => 'Rascunhos', 'icon' => 'heroicon-o-eye-slash', 'tone' => 'gray', 'value' => $stats[Alert::STATUS_DRAFT],
            'note' => 'Não aparecem no site', 'click' => "\$set('status', 'draft')", 'active' => $status === Alert::STATUS_DRAFT],
        ['label' => 'Agendados', 'icon' => 'heroicon-o-calendar', 'tone' => 'info', 'value' => $stats[Alert::STATUS_SCHEDULED],
            'note' => $stats[Alert::STATUS_EXPIRED] ? $stats[Alert::STATUS_EXPIRED].' encerrado(s)' : 'Entram no ar em breve', 'click' => "\$set('status', 'scheduled')", 'active' => $status === Alert::STATUS_SCHEDULED],
    ];
@endphp

<x-filament-panels::page>
    @include('filament.content-sections.styles')
    @include('filament.alerts.styles')

    <div class="al cs cx-ui">
        {{-- Indicadores: clicar aplica o filtro --}}
        <div class="cx-kpis-ctn">
            <div class="cx-kpis">
                @foreach ($kpis as $kpi)
                    <button
                        type="button"
                        class="cx-kpi cx-tone-{{ $kpi['tone'] }} @if ($kpi['active']) is-active @endif"
                        wire:click="{{ $kpi['click'] }}"
                        aria-pressed="{{ $kpi['active'] ? 'true' : 'false' }}"
                    >
                        <span class="cx-kpi-label">
                            <x-filament::icon :icon="$kpi['icon']" class="cx-icon-sm" />
                            {{ $kpi['label'] }}
                        </span>
                        <span class="cx-kpi-value">{{ $kpi['value'] }}</span>
                        <span class="cx-kpi-note">{{ $kpi['note'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Barra de ferramentas --}}
        <div class="cs-toolbar" role="search" aria-label="Filtrar alertas">
            <x-filament::input.wrapper class="cs-search" prefix-icon="heroicon-m-magnifying-glass" wire:target="search">
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, título ou mensagem…"
                    aria-label="Buscar por nome, título ou mensagem"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper class="cs-filter" prefix-icon="heroicon-m-square-3-stack-3d">
                <x-filament::input.select wire:model.live="type" aria-label="Filtrar por tipo">
                    <option value="">Todos os tipos</option>
                    @foreach ($types as $key => $meta)
                        <option value="{{ $key }}">{{ $meta['label'] }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::input.wrapper class="cs-filter" prefix-icon="heroicon-m-swatch">
                <x-filament::input.select wire:model.live="style" aria-label="Filtrar por estilo">
                    <option value="">Todos os estilos</option>
                    @foreach ($styles as $key => $meta)
                        <option value="{{ $key }}">{{ $meta['label'] }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::input.wrapper class="cs-filter" prefix-icon="heroicon-m-signal">
                <x-filament::input.select wire:model.live="status" aria-label="Filtrar por status">
                    <option value="">Todos os status</option>
                    @foreach (ListAlerts::STATUSES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <div class="cs-toolbar-end">
                <x-filament::input.wrapper class="al-sort" prefix-icon="heroicon-m-arrows-up-down">
                    <x-filament::input.select wire:model.live="sort" aria-label="Ordenar por">
                        @foreach (ListAlerts::SORTS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <div class="cs-seg" role="group" aria-label="Visualização">
                    <button type="button" class="cs-seg-btn cs-seg-btn--icon @if (! $isList) is-active @endif" wire:click="$set('display', 'grade')" aria-pressed="{{ $isList ? 'false' : 'true' }}" title="Grade" aria-label="Ver em grade">
                        <x-filament::icon icon="heroicon-m-squares-2x2" />
                    </button>
                    <button type="button" class="cs-seg-btn cs-seg-btn--icon @if ($isList) is-active @endif" wire:click="$set('display', 'lista')" aria-pressed="{{ $isList ? 'true' : 'false' }}" title="Lista" aria-label="Ver em lista">
                        <x-filament::icon icon="heroicon-m-list-bullet" />
                    </button>
                </div>
            </div>
        </div>

        @if ($this->hasFilters() && $alerts->isNotEmpty())
            <div class="cs-bar" role="status">
                <x-filament::icon icon="heroicon-m-information-circle" />
                <span>Mostrando {{ $alerts->count() }} de {{ $stats['total'] }} alertas.</span>
                <div class="cs-bar-actions">
                    <button type="button" class="cs-text-btn" wire:click="clearFilters">Limpar filtros</button>
                </div>
            </div>
        @endif

        <div class="cs-results" wire:loading.delay.class="is-loading" wire:target="{{ $loadingTargets }}" aria-live="polite">
            @if ($stats['total'] === 0)
                <div class="cs-bar" style="justify-content: center">
                    <div class="cx-empty cx-tone-primary">
                        <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-bell-alert" class="cx-icon" /></span>
                        <p class="cx-empty-title">Nenhum alerta criado</p>
                        <p class="cx-empty-desc">Alertas avisam os visitantes sobre promoções, horários e novidades com um popup, um toast ou uma faixa no site.</p>
                        @if ($canCreate)
                            <x-filament::button icon="heroicon-m-plus" :href="AlertResource::getUrl('create')" tag="a">Criar primeiro alerta</x-filament::button>
                        @endif
                    </div>
                </div>
            @elseif ($alerts->isEmpty())
                <div class="cs-bar" style="justify-content: center">
                    <div class="cx-empty cx-tone-gray">
                        <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-magnifying-glass" class="cx-icon" /></span>
                        <p class="cx-empty-title">Nenhum alerta encontrado</p>
                        <p class="cx-empty-desc">Nenhum alerta corresponde à busca e aos filtros escolhidos.</p>
                        <x-filament::button color="gray" icon="heroicon-m-x-mark" wire:click="clearFilters">Limpar filtros</x-filament::button>
                    </div>
                </div>
            @else
                <div @class(['al-grid', 'al-grid--list' => $isList]) role="list">
                    @foreach ($alerts as $alert)
                        @php
                            $id = $alert->id;
                            $type = $types[$alert->type] ?? ['label' => $alert->type, 'icon' => 'heroicon-o-bell'];
                            $style = $styles[$alert->style] ?? $styles[Alert::STYLE_INFO];
                            $alertStatus = $alert->status();
                            $statusMeta = $statusCatalog[$alertStatus];
                            $position = $alert->resolvedPosition();
                            [$positionIcon, $positionLabel] = $positionText[$position];
                            $editable = AlertResource::canEdit($alert);
                            $editUrl = $editable ? AlertResource::getUrl('edit', ['record' => $alert]) : null;
                            $order = $positions[$id];

                            [$dateIcon, $dateText] = match ($alertStatus) {
                                Alert::STATUS_SCHEDULED => ['heroicon-m-calendar', 'Começa em '.$alert->start_at->format('d/m/Y')],
                                Alert::STATUS_EXPIRED => ['heroicon-m-clock', 'Encerrado em '.$alert->end_at->format('d/m/Y')],
                                Alert::STATUS_ACTIVE => ['heroicon-m-calendar', $alert->end_at
                                    ? 'Até '.$alert->end_at->format('d/m/Y')
                                    : 'Desde '.($alert->start_at ?? $alert->created_at)?->format('d/m/Y')],
                                default => ['heroicon-m-pencil-square', 'Editado em '.$alert->updated_at?->format('d/m/Y')],
                            };
                        @endphp

                        <article
                            wire:key="al-card-{{ $id }}"
                            role="listitem"
                            aria-labelledby="al-name-{{ $id }}"
                            @class(['al-card', 'is-off' => $alertStatus !== Alert::STATUS_ACTIVE])
                        >
                            {{-- Miniatura: o alerta sobre uma página esquematizada. Clicar pré-visualiza no site --}}
                            <button
                                type="button"
                                class="al-thumb is-{{ $alert->type }} is-{{ $position }}"
                                wire:click="mountAction('preview', { record: {{ $id }} })"
                                aria-label="Pré-visualizar {{ $alert->name }} no site"
                                title="Pré-visualizar no site"
                            >
                                <span class="al-thumb-page" aria-hidden="true">
                                    <span class="al-thumb-nav"></span>
                                    <span class="al-thumb-line" style="--w: 58%"></span>
                                    <span class="al-thumb-line" style="--w: 40%"></span>
                                    <span class="al-thumb-line is-thin" style="--w: 70%"></span>
                                </span>
                                <span class="al-mini" style="{{ \App\Support\AlertAppearance::cssVariables($alert->style) }}" aria-hidden="true">
                                    <span class="al-mini-icon">{!! \App\Support\AlertAppearance::icon($alert->style) !!}</span>
                                    <span class="al-mini-text">
                                        @if ($alert->title)
                                            <span class="al-mini-title">{{ $alert->title }}</span>
                                        @endif
                                        <span class="al-mini-msg">{{ $alert->message }}</span>
                                        @if ($alert->hasCta())
                                            <span class="al-mini-cta">{{ $alert->cta_label }} →</span>
                                        @endif
                                    </span>
                                    @if ($alert->is_dismissible)
                                        <x-filament::icon icon="heroicon-m-x-mark" class="al-mini-close" />
                                    @endif
                                </span>
                            </button>

                            <div class="al-head">
                                <h3 class="al-name" id="al-name-{{ $id }}" title="{{ $alert->name }}">{{ $alert->name }}</h3>
                                <button
                                    type="button"
                                    role="switch"
                                    aria-checked="{{ $alert->is_active ? 'true' : 'false' }}"
                                    aria-label="Alerta {{ $alert->name }} publicado"
                                    title="{{ $alert->is_active ? 'Publicado. Clique para desativar' : 'Rascunho. Clique para publicar' }}"
                                    @class(['cs-switch', 'is-on' => $alert->is_active])
                                    wire:click="mountAction('toggleActive', { record: {{ $id }} })"
                                    wire:loading.attr="disabled"
                                    wire:target="mountAction"
                                    @disabled(! $editable)
                                >
                                    <span class="cs-switch-knob"><x-filament::icon icon="heroicon-m-check" /></span>
                                </button>
                                <span class="cx-badge cx-tone-{{ $statusMeta['color'] }} al-state">
                                    <x-filament::icon :icon="$statusMeta['icon']" />
                                    {{ $statusMeta['label'] }}
                                </span>
                            </div>

                            <div class="al-badges">
                                <span class="cx-badge cx-tone-primary"><x-filament::icon :icon="$type['icon']" /> {{ $type['label'] }}</span>
                                <span class="cx-badge cx-tone-{{ $style['color'] }}"><x-filament::icon :icon="$style['icon']" /> {{ $style['label'] }}</span>
                            </div>

                            <p class="al-desc" title="{{ $alert->message }}">{{ $alert->title ?: $alert->message }}</p>

                            <div class="al-meta">
                                <span><x-filament::icon :icon="$positionIcon" /> {{ $positionLabel }}</span>
                                <span><x-filament::icon :icon="$dateIcon" /> {{ $dateText }}</span>
                            </div>

                            <div class="al-foot">
                                @if ($editUrl)
                                    <a href="{{ $editUrl }}" class="al-edit">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="cx-icon-sm" />
                                        Editar
                                    </a>
                                @else
                                    <span class="al-edit is-disabled">Somente leitura</span>
                                @endif

                                <x-filament::dropdown placement="bottom-end" width="xs">
                                    <x-slot name="trigger">
                                        <button type="button" class="al-more" aria-label="Mais ações para {{ $alert->name }}" title="Mais ações">
                                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" />
                                        </button>
                                    </x-slot>

                                    <x-filament::dropdown.list>
                                        <x-filament::dropdown.list.item icon="heroicon-m-eye" x-on:click="close()" wire:click="mountAction('preview', { record: {{ $id }} })">
                                            Pré-visualizar no site
                                        </x-filament::dropdown.list.item>
                                        @if ($editable)
                                            <x-filament::dropdown.list.item :icon="$alert->is_active ? 'heroicon-m-eye-slash' : 'heroicon-m-paper-airplane'" x-on:click="close()" wire:click="mountAction('toggleActive', { record: {{ $id }} })">
                                                {{ $alert->is_active ? 'Desativar' : 'Publicar' }}
                                            </x-filament::dropdown.list.item>
                                        @endif
                                        @if ($canCreate)
                                            <x-filament::dropdown.list.item icon="heroicon-m-document-duplicate" x-on:click="close()" wire:click="mountAction('duplicate', { record: {{ $id }} })">
                                                Duplicar
                                            </x-filament::dropdown.list.item>
                                        @endif
                                    </x-filament::dropdown.list>

                                    @if ($canReorder)
                                        <x-filament::dropdown.list>
                                            @if ($order > 1)
                                                <x-filament::dropdown.list.item icon="heroicon-m-arrow-up" x-on:click="close()" wire:click="moveAlert({{ $id }}, -1)">Mover para cima</x-filament::dropdown.list.item>
                                            @endif
                                            @if ($order < $stats['total'])
                                                <x-filament::dropdown.list.item icon="heroicon-m-arrow-down" x-on:click="close()" wire:click="moveAlert({{ $id }}, 1)">Mover para baixo</x-filament::dropdown.list.item>
                                            @endif
                                        </x-filament::dropdown.list>
                                    @endif

                                    @if (AlertResource::canDelete($alert))
                                        <x-filament::dropdown.list>
                                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" x-on:click="close()" wire:click="mountAction('delete', { record: {{ $id }} })">
                                                Excluir
                                            </x-filament::dropdown.list.item>
                                        </x-filament::dropdown.list>
                                    @endif
                                </x-filament::dropdown>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
