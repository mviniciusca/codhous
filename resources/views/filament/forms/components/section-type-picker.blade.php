{{-- Biblioteca de componentes do construtor de seções (coluna esquerda). Prefixo stp- --}}
@php
    use Illuminate\Support\Str;

    $types = $getTypes();
    $categories = $getCategories();
    $usage = $getUsage();
    $statePath = $getStatePath();
    $state = $getState();
    $isDisabled = $isDisabled();

    // Texto de busca sem acentos, para "servico" encontrar "Serviços"
    $index = collect($types)->map(fn (array $type, string $key): array => [
        'text' => Str::lower(Str::ascii(implode(' ', [$type['label'], $type['description'], $type['keywords'] ?? '', $key]))),
        'categories' => $type['categories'] ?? [],
    ])->all();
@endphp

@include('filament.forms.components.section-type-picker.styles')

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if ($isDisabled)
        {{-- Edição: o tipo é fixo, então a biblioteca mostra só o componente atual --}}
        @php $type = $types[$state] ?? null; @endphp

        <div class="stp cx-ui">
            <div class="stp-head">
                <p class="stp-title">Componente</p>
                <p class="stp-desc">O tipo define os campos de conteúdo e não pode ser alterado depois de criado.</p>
            </div>

            @if ($type)
                <div class="stp-list">
                    <div class="stp-card is-selected is-locked">
                        <div class="stp-thumb" aria-hidden="true">
                            @if ($preview = $getPreviewView($state))
                                @include($preview)
                            @endif
                        </div>
                        <div class="stp-meta">
                            <span class="stp-meta-icon"><x-filament::icon :icon="$type['icon']" /></span>
                            <div class="stp-meta-text">
                                <span class="stp-meta-title">{{ $type['label'] }}</span>
                                <span class="stp-meta-desc">{{ $type['description'] }}</span>
                            </div>
                        </div>
                        <span class="stp-check stp-check--lock" aria-hidden="true"><x-filament::icon icon="heroicon-m-lock-closed" /></span>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div
            class="stp cx-ui"
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                query: '',
                category: 'all',
                index: @js($index),

                normalize(value) {
                    return (value ?? '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim()
                },
                matchesQuery(type) {
                    const words = this.normalize(this.query).split(/\s+/).filter(Boolean)
                    return words.every((word) => this.index[type].text.includes(word))
                },
                inCategory(type, category) {
                    return category === 'all' || this.index[type].categories.includes(category)
                },
                isVisible(type) {
                    return this.matchesQuery(type) && this.inCategory(type, this.category)
                },
                count(category) {
                    return Object.keys(this.index).filter((type) => this.matchesQuery(type) && this.inCategory(type, category)).length
                },
                visibleTypes() {
                    return Object.keys(this.index).filter((type) => this.isVisible(type))
                },
                select(type) {
                    this.state = type
                },
                move(step) {
                    const list = this.visibleTypes()
                    if (! list.length) return

                    const current = list.indexOf(this.state)
                    const next = current < 0 ? 0 : Math.min(Math.max(current + step, 0), list.length - 1)

                    this.state = list[next]
                    this.$nextTick(() => this.$refs.list.querySelector(`[data-type='${list[next]}']`)?.focus())
                },
                // Duplo clique escolhe e já avança para a próxima etapa do inspetor
                selectAndContinue(type) {
                    this.select(type)
                    this.$nextTick(() => document.querySelector('.sb-inspector [data-wizard-next] button')?.click())
                },
                clearFilters() {
                    this.query = ''
                    this.category = 'all'
                    this.$refs.search.focus()
                },
            }"
        >
            <div class="stp-head">
                <p class="stp-title">Biblioteca de componentes</p>
                <p class="stp-desc">Selecione o tipo de seção que deseja criar.</p>

                <label class="stp-search">
                    <x-filament::icon icon="heroicon-m-magnifying-glass" />
                    <input
                        type="search"
                        x-ref="search"
                        x-model.debounce.100ms="query"
                        x-on:keydown.escape="query = ''"
                        x-on:keydown.down.prevent="$refs.list.querySelector('.stp-card:not([style*=\'display: none\'])')?.focus()"
                        placeholder="Buscar componentes…"
                        aria-label="Buscar componentes"
                        autocomplete="off"
                    />
                </label>

                <div class="stp-cats" role="toolbar" aria-label="Categorias">
                    <button
                        type="button"
                        class="stp-cat"
                        x-bind:class="{ 'is-active': category === 'all' }"
                        x-bind:aria-pressed="category === 'all'"
                        x-on:click="category = 'all'"
                    >
                        Todas <span class="stp-cat-count" x-text="count('all')">{{ count($types) }}</span>
                    </button>

                    @foreach ($categories as $key => $category)
                        <button
                            type="button"
                            class="stp-cat"
                            x-bind:class="{ 'is-active': category === @js($key), 'is-empty': count(@js($key)) === 0 }"
                            x-bind:aria-pressed="category === @js($key)"
                            x-on:click="category = @js($key)"
                        >
                            <x-filament::icon :icon="$category['icon']" />
                            {{ $category['label'] }}
                            <span class="stp-cat-count" x-text="count(@js($key))">
                                {{ collect($types)->filter(fn ($type) => in_array($key, $type['categories'] ?? []))->count() }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div
                class="stp-list"
                role="radiogroup"
                aria-label="{{ $getLabel() }}"
                x-ref="list"
                x-on:keydown.down.prevent="move(1)"
                x-on:keydown.right.prevent="move(1)"
                x-on:keydown.up.prevent="move(-1)"
                x-on:keydown.left.prevent="move(-1)"
            >
                @foreach ($types as $key => $type)
                    @php $inUse = $usage[$key] ?? 0; @endphp

                    <div
                        role="radio"
                        data-type="{{ $key }}"
                        class="stp-card"
                        x-show="isVisible(@js($key))"
                        x-bind:class="{ 'is-selected': state === @js($key) }"
                        x-bind:aria-checked="state === @js($key)"
                        x-bind:tabindex="state === @js($key) || (! state && {{ $loop->first ? 'true' : 'false' }}) ? 0 : -1"
                        x-on:click="select(@js($key))"
                        x-on:dblclick="selectAndContinue(@js($key))"
                        x-on:keydown.enter.prevent="select(@js($key))"
                        x-on:keydown.space.prevent="select(@js($key))"
                        aria-label="{{ $type['label'] }}"
                    >
                        <div class="stp-thumb" aria-hidden="true">
                            @if ($preview = $getPreviewView($key))
                                @include($preview)
                            @endif
                        </div>

                        <div class="stp-meta">
                            <span class="stp-meta-icon"><x-filament::icon :icon="$type['icon']" /></span>
                            <div class="stp-meta-text">
                                <span class="stp-meta-title">
                                    {{ $type['label'] }}
                                    @if ($inUse)
                                        <span class="stp-badge" title="Já existe {{ $inUse }} {{ $inUse === 1 ? 'seção' : 'seções' }} deste tipo">{{ $inUse }} em uso</span>
                                    @endif
                                </span>
                                <span class="stp-meta-desc">{{ $type['description'] }}</span>
                            </div>
                        </div>

                        <span class="stp-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </span>
                    </div>
                @endforeach

                <div class="stp-empty" x-show="visibleTypes().length === 0" x-cloak>
                    <x-filament::icon icon="heroicon-o-magnifying-glass" />
                    <p class="stp-empty-title">Nenhum componente encontrado</p>
                    <p class="stp-empty-desc">Tente outro termo ou veja todas as categorias.</p>
                    <button type="button" class="stp-empty-btn" x-on:click="clearFilters()">Limpar busca</button>
                </div>
            </div>
        </div>
    @endif
</x-dynamic-component>
