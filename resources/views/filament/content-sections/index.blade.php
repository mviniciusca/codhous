{{-- Seções do site: indicadores, filtros e cards com o skeleton de cada tipo. Prefixo cs- --}}
@php
    use App\Filament\Resources\ContentSectionResource;
    use App\Filament\Resources\PageResource;

    $ids = $sections->pluck('id')->all();
    $isList = $display === 'lista';
    $canBulk = $canDeleteAny;
    $canOpenPages = PageResource::canAccess();
    $loadingTargets = 'search,type,status,usage,display,clearFilters,reorderSections,moveSection';

    $kpis = [
        ['label' => 'Total de seções', 'icon' => 'heroicon-o-cube', 'tone' => 'primary', 'value' => $stats['total'],
            'note' => $stats['total'] - $stats['active'].' inativas', 'click' => 'clearFilters', 'active' => ! $this->hasFilters()],
        ['label' => 'Ativas', 'icon' => 'heroicon-o-check-circle', 'tone' => 'success', 'value' => $stats['active'],
            'note' => 'Disponíveis para o site', 'click' => "\$set('status', 'ativas')", 'active' => $status === 'ativas' && $usage === ''],
        ['label' => 'Em uso nas páginas', 'icon' => 'heroicon-o-document-text', 'tone' => 'info', 'value' => $stats['used'],
            'note' => $stats['total'] - $stats['used'].' sem uso', 'click' => "\$set('usage', 'paginas')", 'active' => $usage === 'paginas'],
        ['label' => 'Na homepage', 'icon' => 'heroicon-o-home', 'tone' => 'primary', 'value' => $stats['home'],
            'note' => 'Na página inicial', 'click' => "\$set('usage', 'home')", 'active' => $usage === 'home'],
    ];
@endphp

<x-filament-panels::page>
    @include('filament.forms.components.section-type-picker.styles')
    @include('filament.content-sections.styles')

    <div class="cs cx-ui">
        {{-- Indicadores: cadastradas, ativas e efetivamente usadas. Clicar aplica o filtro --}}
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
        <div class="cs-toolbar" role="search" aria-label="Filtrar seções">
            <x-filament::input.wrapper class="cs-search" prefix-icon="heroicon-m-magnifying-glass" wire:target="search">
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, slug ou tipo…"
                    aria-label="Buscar por nome, slug ou tipo"
                    autocomplete="off"
                />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper class="cs-filter" prefix-icon="heroicon-m-funnel">
                <x-filament::input.select wire:model.live="type" aria-label="Filtrar por tipo">
                    <option value="">Todos os tipos</option>
                    @foreach ($catalog as $key => $meta)
                        <option value="{{ $key }}">{{ $meta['label'] }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::input.wrapper class="cs-filter" prefix-icon="heroicon-m-document-text">
                <x-filament::input.select wire:model.live="usage" aria-label="Filtrar por uso nas páginas">
                    <option value="">Qualquer uso</option>
                    @foreach (\App\Filament\Resources\ContentSectionResource\Pages\ListContentSections::USAGES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <div class="cs-toolbar-end">
                <div class="cs-seg" role="group" aria-label="Status">
                    @foreach (\App\Filament\Resources\ContentSectionResource\Pages\ListContentSections::STATUSES as $key => $label)
                        <button
                            type="button"
                            class="cs-seg-btn @if ($status === $key) is-active @endif"
                            wire:click="$set('status', '{{ $key }}')"
                            aria-pressed="{{ $status === $key ? 'true' : 'false' }}"
                        >
                            {{ $label }} <span class="cs-seg-count">({{ $statusCounts[$key] }})</span>
                        </button>
                    @endforeach
                </div>

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

        {{-- Seleção em massa --}}
        @if (count($selected))
            <div class="cs-bar cs-bar--selection" role="region" aria-label="Ações em massa">
                <span><strong>{{ trans_choice(':count seção selecionada|:count seções selecionadas', count($selected)) }}</strong></span>
                @if (count($selected) < count($ids))
                    <button type="button" class="cs-text-btn" wire:click="$set('selected', @js($ids))">Selecionar as {{ count($ids) }} visíveis</button>
                @endif
                <button type="button" class="cs-text-btn" wire:click="$set('selected', [])">Limpar seleção</button>

                <div class="cs-bar-actions">
                    <x-filament::button size="sm" color="gray" icon="heroicon-m-eye" wire:click="mountAction('bulkActivate')">Ativar</x-filament::button>
                    <x-filament::button size="sm" color="gray" icon="heroicon-m-eye-slash" wire:click="mountAction('bulkDeactivate')">Desativar</x-filament::button>
                    @if ($canDeleteAny)
                        <x-filament::button size="sm" color="danger" icon="heroicon-m-trash" wire:click="mountAction('bulkDelete')">Excluir</x-filament::button>
                    @endif
                </div>
            </div>
        @elseif ($this->hasFilters() && $sections->isNotEmpty())
            <div class="cs-bar" role="status">
                <x-filament::icon icon="heroicon-m-information-circle" />
                <span>Mostrando {{ $sections->count() }} de {{ $stats['total'] }} seções. Para reordenar arrastando, limpe os filtros.</span>
                <div class="cs-bar-actions">
                    <button type="button" class="cs-text-btn" wire:click="clearFilters">Limpar filtros</button>
                </div>
            </div>
        @endif

        <div class="cs-results" wire:loading.delay.class="is-loading" wire:target="{{ $loadingTargets }}" aria-live="polite">
            @if ($stats['total'] === 0)
                <div class="cs-bar" style="justify-content: center">
                    <div class="cx-empty cx-tone-primary">
                        <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-squares-2x2" class="cx-icon" /></span>
                        <p class="cx-empty-title">Nenhuma seção criada</p>
                        <p class="cx-empty-desc">Seções são módulos prontos (hero, FAQ, depoimentos…) que você monta uma vez e reutiliza em várias páginas.</p>
                        @if ($canCreate)
                            <x-filament::button icon="heroicon-m-plus" :href="ContentSectionResource::getUrl('create')" tag="a">Criar primeira seção</x-filament::button>
                        @endif
                    </div>
                </div>
            @elseif ($sections->isEmpty())
                <div class="cs-bar" style="justify-content: center">
                    <div class="cx-empty cx-tone-gray">
                        <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-magnifying-glass" class="cx-icon" /></span>
                        <p class="cx-empty-title">Nenhuma seção encontrada</p>
                        <p class="cx-empty-desc">Nenhuma seção corresponde à busca e aos filtros escolhidos.</p>
                        <x-filament::button color="gray" icon="heroicon-m-x-mark" wire:click="clearFilters">Limpar filtros</x-filament::button>
                    </div>
                </div>
            @else
                <div
                    @class(['cs-grid', 'cs-grid--list' => $isList])
                    wire:key="cs-grid-{{ md5(implode(',', $ids).$display) }}"
                    role="list"
                    @if ($canReorder)
                        x-data="{
                            order: @js($ids),
                            initial: @js($ids),
                            dragging: null,
                            armed: null,
                            start(id, event) {
                                this.dragging = id
                                event.dataTransfer.effectAllowed = 'move'
                                event.dataTransfer.setData('text/plain', String(id))
                            },
                            enter(id) {
                                if (this.dragging === null || id === this.dragging) return
                                const to = this.order.indexOf(id)
                                this.order.splice(this.order.indexOf(this.dragging), 1)
                                this.order.splice(to, 0, this.dragging)
                            },
                            drop() {
                                const changed = this.order.join() !== this.initial.join()
                                this.dragging = null
                                this.armed = null
                                if (changed) {
                                    this.initial = [...this.order]
                                    this.$wire.reorderSections(this.order)
                                }
                            },
                            end() {
                                if (this.dragging !== null) this.order = [...this.initial]
                                this.dragging = null
                                this.armed = null
                            },
                            move(id, direction) {
                                this.$wire.moveSection(id, direction).then(() => this.$nextTick(() => document.querySelector(`[data-cs-handle='${id}']`)?.focus()))
                            },
                        }"
                        x-on:dragover.prevent=""
                        x-on:drop.prevent="drop()"
                    @endif
                >
                    @foreach ($sections as $section)
                        @php
                            $id = $section->id;
                            $meta = $catalog[$section->type] ?? ['label' => $section->type, 'icon' => 'heroicon-o-squares-2x2'];
                            $preview = "filament.forms.components.section-type-picker.previews.{$section->type}";
                            $editable = $canEdit($section);
                            $editUrl = $editable ? ContentSectionResource::getUrl('edit', ['record' => $section]) : null;
                            $position = $positions[$id];
                            $isSelected = in_array($id, array_map('intval', $selected), true);

                            // Uso nas páginas: homepage primeiro, depois as demais
                            $usages = collect($usageMap[$id] ?? []);
                            $home = $usages->firstWhere('is_home', true);
                            $otherPages = $usages->where('is_home', false)->pluck('title')->unique()->values();
                            $usageDetail = $usages->map(fn (array $u): string => $u['title'].($u['is_home'] ? ' (homepage)' : '').' · posição '.$u['position'].($u['is_visible'] ? '' : ' · página oculta'))->implode("\n");

                            if ($usages->isEmpty()) {
                                [$usageTone, $usageIcon, $usageText] = ['gray', 'heroicon-m-minus-circle', 'Não utilizada'];
                            } else {
                                $usageText = $home
                                    ? 'Homepage · Posição '.$home['position'].($otherPages->isNotEmpty() ? ' · +'.trans_choice(':count página|:count páginas', $otherPages->count()) : '')
                                    : ($otherPages->count() === 1 ? 'Em uso em '.$otherPages->first() : 'Em uso em '.$otherPages->count().' páginas');
                                [$usageTone, $usageIcon] = $home ? ['primary', 'heroicon-m-home'] : ['info', 'heroicon-m-document-text'];

                                if (! $section->is_active) {
                                    [$usageTone, $usageIcon, $usageText] = ['warning', 'heroicon-m-eye-slash', 'Oculta · '.$usageText];
                                }
                            }
                        @endphp

                        <article
                            wire:key="cs-card-{{ $id }}"
                            role="listitem"
                            aria-labelledby="cs-name-{{ $id }}"
                            @class(['cs-card', 'is-inactive' => ! $section->is_active, 'is-selected' => $isSelected])
                            @if ($canReorder)
                                x-bind:style="{ order: order.indexOf({{ $id }}) }"
                                x-bind:draggable="armed === {{ $id }}"
                                x-bind:class="{ 'is-dragging': dragging === {{ $id }} }"
                                x-on:dragstart="start({{ $id }}, $event)"
                                x-on:dragenter.prevent="enter({{ $id }})"
                                x-on:dragend="end()"
                            @endif
                        >
                            <div class="cs-lead">
                                @if ($canBulk)
                                    <input
                                        type="checkbox"
                                        class="cs-check fi-checkbox-input"
                                        value="{{ $id }}"
                                        wire:model.live="selected"
                                        aria-label="Selecionar {{ $section->name }}"
                                    />
                                @endif

                                @if ($canReorder)
                                    <button
                                        type="button"
                                        class="cs-handle"
                                        data-cs-handle="{{ $id }}"
                                        title="Arraste para reordenar"
                                        aria-label="Reordenar {{ $section->name }}, posição {{ $position }} de {{ $stats['total'] }}. Use as setas para mover."
                                        x-on:mousedown="armed = {{ $id }}"
                                        x-on:mouseup="armed = null"
                                        x-on:keydown.up.prevent="move({{ $id }}, -1)"
                                        x-on:keydown.left.prevent="move({{ $id }}, -1)"
                                        x-on:keydown.down.prevent="move({{ $id }}, 1)"
                                        x-on:keydown.right.prevent="move({{ $id }}, 1)"
                                    >
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="7" cy="4.5" r="1.5"/><circle cx="13" cy="4.5" r="1.5"/><circle cx="7" cy="10" r="1.5"/><circle cx="13" cy="10" r="1.5"/><circle cx="7" cy="15.5" r="1.5"/><circle cx="13" cy="15.5" r="1.5"/></svg>
                                    </button>
                                @endif

                                <span class="cs-pos" title="Posição na lista de seções">{{ $position }}</span>
                            </div>

                            <div class="cs-switch-wrap">
                                <button
                                    type="button"
                                    role="switch"
                                    aria-checked="{{ $section->is_active ? 'true' : 'false' }}"
                                    aria-label="Seção {{ $section->name }} ativa"
                                    title="{{ $section->is_active ? 'Ativa. Clique para desativar' : 'Inativa. Clique para ativar' }}"
                                    @class(['cs-switch', 'is-on' => $section->is_active])
                                    wire:click="mountAction('toggleActive', { record: {{ $id }} })"
                                    wire:loading.attr="disabled"
                                    wire:target="mountAction"
                                    @disabled(! $editable)
                                >
                                    <span class="cs-switch-knob"><x-filament::icon icon="heroicon-m-check" /></span>
                                </button>
                            </div>

                            {{-- Skeleton do tipo (o mesmo da biblioteca de componentes); clicar abre o editor --}}
                            <{{ $editUrl ? 'a' : 'div' }} @if ($editUrl) href="{{ $editUrl }}" tabindex="-1" @endif class="cs-thumb stp-thumb stp-tokens" aria-hidden="true">
                                <div class="cs-thumb-frame">
                                    @includeIf($preview)
                                </div>
                            </{{ $editUrl ? 'a' : 'div' }}>

                            <div class="cs-info">
                                <span class="cs-icon" aria-hidden="true"><x-filament::icon :icon="$meta['icon']" /></span>
                                <div class="cs-text">
                                    <h3 class="cs-name" id="cs-name-{{ $id }}" title="{{ $section->name }}">{{ $section->name }}</h3>
                                    <code class="cs-slug" title="Slug">{{ $section->slug }}</code>
                                </div>
                                <div class="cs-badges">
                                    <span class="cx-badge cx-tone-primary cs-type" title="Tipo: {{ $meta['label'] }}">{{ $meta['label'] }}</span>
                                    @if ($section->is_active)
                                        <span class="cx-badge cx-tone-success"><x-filament::icon icon="heroicon-m-check-circle" /> Ativa</span>
                                    @else
                                        <span class="cx-badge cx-tone-gray"><x-filament::icon icon="heroicon-m-pause-circle" /> Inativa</span>
                                    @endif
                                </div>
                            </div>

                            <p class="cs-usage cx-tone-{{ $usageTone }}" @if ($usageDetail) title="{{ $usageDetail }}" @endif>
                                <x-filament::icon :icon="$usageIcon" />
                                <span>{{ $usageText }}</span>
                                @if ($usageDetail)
                                    <span class="cx-sr">: {{ str_replace("\n", '; ', $usageDetail) }}</span>
                                @endif
                            </p>

                            <div class="cs-foot">
                                @if ($editUrl)
                                    <a href="{{ $editUrl }}" class="cx-link">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="cx-icon-sm" />
                                        Editar conteúdo
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                <x-filament::dropdown placement="bottom-end" width="xs">
                                    <x-slot name="trigger">
                                        <button type="button" class="cs-icon-btn" aria-label="Mais ações para {{ $section->name }}" title="Mais ações">
                                            <x-filament::icon icon="heroicon-m-ellipsis-vertical" />
                                        </button>
                                    </x-slot>

                                    <x-filament::dropdown.list>
                                        @if ($canCreate)
                                            <x-filament::dropdown.list.item icon="heroicon-m-document-duplicate" x-on:click="close()" wire:click="mountAction('duplicate', { record: {{ $id }} })">
                                                Duplicar
                                            </x-filament::dropdown.list.item>
                                        @endif
                                        @if ($editable)
                                            <x-filament::dropdown.list.item :icon="$section->is_active ? 'heroicon-m-eye-slash' : 'heroicon-m-eye'" x-on:click="close()" wire:click="mountAction('toggleActive', { record: {{ $id }} })">
                                                {{ $section->is_active ? 'Desativar' : 'Ativar' }}
                                            </x-filament::dropdown.list.item>
                                        @endif
                                        @if ($canReorder)
                                            @if ($position > 1)
                                                <x-filament::dropdown.list.item icon="heroicon-m-arrow-up" x-on:click="close()" wire:click="moveSection({{ $id }}, -1)">Mover para cima</x-filament::dropdown.list.item>
                                            @endif
                                            @if ($position < $stats['total'])
                                                <x-filament::dropdown.list.item icon="heroicon-m-arrow-down" x-on:click="close()" wire:click="moveSection({{ $id }}, 1)">Mover para baixo</x-filament::dropdown.list.item>
                                            @endif
                                        @endif
                                    </x-filament::dropdown.list>

                                    @if ($canOpenPages && $usages->isNotEmpty())
                                        <x-filament::dropdown.list>
                                            @foreach ($usages->unique('page_id')->take(4) as $u)
                                                <x-filament::dropdown.list.item tag="a" icon="heroicon-m-arrow-top-right-on-square" :href="PageResource::getUrl('edit', ['record' => $u['page_id']])">
                                                    Abrir página: {{ $u['is_home'] ? 'Homepage' : $u['title'] }}
                                                </x-filament::dropdown.list.item>
                                            @endforeach
                                        </x-filament::dropdown.list>
                                    @endif

                                    @if (ContentSectionResource::canDelete($section))
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
