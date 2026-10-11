{{-- Seletor visual de templates do Page Builder (usado na ação "Templates" de PageResource) --}}
@php
    $statePath = $getStatePath();
    $usedCategories = collect($templates)->pluck('category')->unique();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <style>
        .pbt { --pbt-border: rgba(15, 23, 42, .1); --pbt-muted: rgb(var(--gray-500)); --pbt-text: rgb(var(--gray-950)); --pbt-surface: #fff; --pbt-soft: rgb(var(--gray-50)); --pbt-accent: var(--primary-600); }
        .dark .pbt { --pbt-border: rgba(255, 255, 255, .1); --pbt-muted: rgb(var(--gray-400)); --pbt-text: #fff; --pbt-surface: rgba(255, 255, 255, .03); --pbt-soft: rgba(255, 255, 255, .04); --pbt-accent: var(--primary-400); }

        .pbt-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
        .pbt-tab { padding: 6px 12px; font-size: 13px; font-weight: 600; border-radius: 999px; color: var(--pbt-muted); border: 1px solid var(--pbt-border); background: var(--pbt-surface); transition: all .15s; }
        .pbt-tab:hover { color: var(--pbt-text); }
        .pbt-tab.is-active { color: #fff; background: rgb(var(--pbt-accent)); border-color: transparent; }
        .pbt-tab-count { margin-left: 4px; opacity: .7; font-weight: 500; }

        .pbt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; max-height: 58vh; overflow-y: auto; padding: 2px; }

        .pbt-card { position: relative; display: flex; flex-direction: column; gap: 12px; padding: 16px; text-align: left; border-radius: 16px; border: 1.5px solid var(--pbt-border); background: var(--pbt-surface); cursor: pointer; transition: border-color .15s, box-shadow .15s, transform .15s; }
        .pbt-card:hover { border-color: rgba(var(--primary-500), .5); transform: translateY(-1px); box-shadow: 0 8px 24px -14px rgba(15, 23, 42, .35); }
        .pbt-card:focus-visible { outline: none; border-color: rgba(var(--primary-500), .7); }
        .pbt-card.is-selected { border-color: rgb(var(--pbt-accent)); box-shadow: 0 0 0 3px rgba(var(--primary-500), .18); }

        .pbt-card-head { display: flex; align-items: flex-start; gap: 12px; }
        .pbt-card-icon { display: grid; place-items: center; flex-shrink: 0; width: 40px; height: 40px; border-radius: 12px; color: rgb(var(--pbt-accent)); background: rgba(var(--primary-500), .1); }
        .pbt-card-icon svg { width: 20px; height: 20px; }
        .pbt-card-title { font-size: 14px; font-weight: 700; color: var(--pbt-text); line-height: 1.3; }
        .pbt-card-meta { margin-top: 2px; font-size: 11px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--pbt-muted); }
        .pbt-card-desc { font-size: 12.5px; line-height: 1.5; color: var(--pbt-muted); }
        .pbt-check { position: absolute; top: 12px; right: 12px; width: 22px; height: 22px; color: rgb(var(--pbt-accent)); }

        /* Mini wireframe: a sequência de blocos da página */
        .pbt-outline { display: flex; flex-direction: column; gap: 3px; margin-top: auto; padding: 8px; border-radius: 10px; background: var(--pbt-soft); }
        .pbt-row { display: flex; align-items: center; gap: 6px; padding: 3px 6px; font-size: 11.5px; color: var(--pbt-text); border-radius: 6px; }
        .pbt-row svg { flex-shrink: 0; width: 13px; height: 13px; color: var(--pbt-muted); }
        .pbt-row-label { flex: 1; min-width: 0; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .pbt-row-tag { flex-shrink: 0; padding: 0 6px; font-size: 10px; font-weight: 600; line-height: 16px; border-radius: 999px; }
        .pbt-row-tag--global { color: rgb(var(--pbt-accent)); background: rgba(var(--primary-500), .12); }
        .pbt-row.is-missing { opacity: .55; }
        .pbt-row.is-missing .pbt-row-label { text-decoration: line-through; }
        .pbt-row-tag--missing { color: rgb(var(--danger-600)); background: rgba(var(--danger-500), .12); }

        .pbt-empty { padding: 32px; text-align: center; font-size: 13px; color: var(--pbt-muted); }
    </style>

    <div
        class="pbt"
        x-data="{ state: $wire.$entangle(@js($statePath)), category: 'all' }"
    >
        <div class="pbt-tabs" role="tablist">
            <button type="button" class="pbt-tab" x-bind:class="{ 'is-active': category === 'all' }" x-on:click="category = 'all'">
                Todos <span class="pbt-tab-count">{{ count($templates) }}</span>
            </button>
            @foreach ($categories as $categoryKey => $categoryLabel)
                @continue(! $usedCategories->contains($categoryKey))
                <button type="button" class="pbt-tab" x-bind:class="{ 'is-active': category === @js($categoryKey) }" x-on:click="category = @js($categoryKey)">
                    {{ $categoryLabel }}
                    <span class="pbt-tab-count">{{ collect($templates)->where('category', $categoryKey)->count() }}</span>
                </button>
            @endforeach
        </div>

        <div class="pbt-grid" role="radiogroup">
            @forelse ($templates as $template)
                <div
                    role="radio"
                    tabindex="0"
                    id="page-template-{{ $template['key'] }}"
                    class="pbt-card"
                    x-show="category === 'all' || category === @js($template['category'])"
                    x-bind:class="{ 'is-selected': state === @js($template['key']) }"
                    x-bind:aria-checked="state === @js($template['key'])"
                    x-on:click="state = @js($template['key'])"
                    x-on:keydown.enter.prevent="state = @js($template['key'])"
                    x-on:keydown.space.prevent="state = @js($template['key'])"
                >
                    <x-filament::icon icon="heroicon-s-check-circle" class="pbt-check" x-show="state === {{ \Illuminate\Support\Js::from($template['key']) }}" x-cloak />

                    <div class="pbt-card-head">
                        <span class="pbt-card-icon"><x-filament::icon :icon="$template['icon']" /></span>
                        <div>
                            <div class="pbt-card-title">{{ $template['label'] }}</div>
                            <div class="pbt-card-meta">
                                {{ $categories[$template['category']] ?? '' }} · {{ count($template['blocks']) }} blocos
                            </div>
                        </div>
                    </div>

                    <p class="pbt-card-desc">{{ $template['description'] }}</p>

                    <div class="pbt-outline">
                        @foreach ($template['blocks'] as $block)
                            <div @class(['pbt-row', 'is-missing' => $block['missing']])>
                                <x-filament::icon :icon="$block['icon']" />
                                <span class="pbt-row-label">{{ $block['label'] }}</span>
                                @if ($block['missing'])
                                    <span class="pbt-row-tag pbt-row-tag--missing" title="Nenhuma seção global ativa deste tipo. O bloco será ignorado.">indisponível</span>
                                @elseif ($block['global'])
                                    <span class="pbt-row-tag pbt-row-tag--global" title="Usa a seção global cadastrada em Seções do site">global</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="pbt-empty">Nenhum template disponível.</div>
            @endforelse
        </div>
    </div>
</x-dynamic-component>
