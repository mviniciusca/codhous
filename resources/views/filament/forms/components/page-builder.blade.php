@php
    use Filament\Forms\Components\Actions\Action;
    use Illuminate\Support\Str;

    $containers = $getChildComponentContainers();
    $blockPickerBlocks = $getBlockPickerBlocks();

    $addAction = $getAction($getAddActionName());
    $cloneAction = $getAction($getCloneActionName());
    $editAction = $getAction($getEditActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $templateAction = $getAction('loadTemplate');
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable() && $addAction->isVisible();
    $isCloneable = $isCloneable();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();
    $hasTemplates = $templateAction?->isVisible() && ! $isDisabled();
    $openTemplates = $hasTemplates ? '$wire.mountFormComponentAction(' . \Illuminate\Support\Js::from($getStatePath()) . ', \'loadTemplate\')' : null;

    $statePath = $getStatePath();

    $livePreviewConfig = [
        'statePath' => $statePath,
        'addAction' => $addAction->getName(),
        'editAction' => $editAction->getName(),
    ];
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @include('filament.page-builder.styles')
    @include('filament.page-builder.script')

    <div
        class="pb-shell"
        x-data="pageBuilderLivePreview(@js($livePreviewConfig))"
        x-bind:class="{ 'is-editing': isEditing }"
        x-on:page-builder-block-added.window="onBlockAdded($event.detail)"
        {{ $attributes->merge($getExtraAttributes(), escape: false) }}
    >
        {{-- ───────────── Coluna 1: Biblioteca de blocos ───────────── --}}
        <aside class="pb-col pb-col--palette" aria-label="Biblioteca de blocos">
            <header class="pb-col-header">
                <div>
                    <h3 class="pb-col-title">Blocos</h3>
                    <p class="pb-col-subtitle">Clique para adicionar ao fim da página</p>
                </div>
            </header>

            <div class="pb-search">
                <x-filament::icon icon="heroicon-m-magnifying-glass" class="pb-search-icon" />
                <input
                    type="search"
                    id="page-builder-block-search"
                    x-model="paletteQuery"
                    placeholder="Buscar bloco…"
                    autocomplete="off"
                />
            </div>

            <div class="pb-palette">
                @foreach ($blockPickerBlocks as $block)
                    @php $blockLabel = (string) $block->getLabel(); @endphp
                    <button
                        type="button"
                        id="page-builder-add-{{ $block->getName() }}"
                        class="pb-palette-item"
                        title="{{ $blockLabel }}"
                        x-show="matches(@js(Str::lower(Str::ascii($blockLabel))))"
                        @if ($isAddable) x-on:click="addBlock(@js($block->getName()))" @else disabled @endif
                    >
                        <span class="pb-palette-icon">
                            <x-filament::icon :icon="$block->getIcon() ?? 'heroicon-o-square-3-stack-3d'" />
                        </span>
                        <span class="pb-palette-label">{{ $blockLabel }}</span>
                        <span class="pb-palette-plus" aria-hidden="true">
                            <x-filament::icon icon="heroicon-m-plus" />
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        {{-- ───────────── Coluna 2: Live Preview ───────────── --}}
        <section class="pb-col pb-col--canvas" aria-label="Live preview" x-ref="canvas">
            <div class="pb-toolbar">
                <div class="pb-toolbar-group">
                    <span class="pb-status" x-bind:data-status="status">
                        <span class="pb-status-dot"></span>
                        <span x-text="statusLabel"></span>
                    </span>
                </div>

                <div class="pb-devices" role="radiogroup" aria-label="Dispositivo">
                    <button type="button" id="page-builder-device-desktop" class="pb-device" x-bind:class="{ 'is-active': device === 'desktop' }" x-on:click="setDevice('desktop')" title="Desktop">
                        <x-filament::icon icon="heroicon-o-computer-desktop" />
                    </button>
                    <button type="button" id="page-builder-device-tablet" class="pb-device" x-bind:class="{ 'is-active': device === 'tablet' }" x-on:click="setDevice('tablet')" title="Tablet">
                        <x-filament::icon icon="heroicon-o-device-tablet" />
                    </button>
                    <button type="button" id="page-builder-device-mobile" class="pb-device" x-bind:class="{ 'is-active': device === 'mobile' }" x-on:click="setDevice('mobile')" title="Celular">
                        <x-filament::icon icon="heroicon-o-device-phone-mobile" />
                    </button>
                </div>

                <div class="pb-toolbar-group pb-toolbar-group--end">
                    <span class="pb-zoom" x-text="Math.round(scale * 100) + '%'" title="Zoom de visualização"></span>
                    <button type="button" id="page-builder-preview-refresh" class="pb-icon-btn" x-on:click="refresh()" title="Atualizar preview">
                        <x-filament::icon icon="heroicon-m-arrow-path" x-bind:class="{ 'pb-spin': status === 'loading' }" />
                    </button>
                    <a id="page-builder-preview-open" class="pb-icon-btn" x-bind:href="url || '#'" target="_blank" rel="noopener" title="Abrir preview em nova aba">
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" />
                    </a>
                </div>
            </div>

            <div class="pb-viewport" x-ref="viewport" wire:ignore>
                <div class="pb-device-frame" x-bind:class="'is-' + device" x-bind:style="frameOuterStyle">
                    <div class="pb-device-inner" x-bind:style="frameInnerStyle">
                        <iframe
                            x-ref="frameA"
                            title="Live preview da página"
                            class="pb-frame"
                            x-bind:class="{ 'is-active': activeFrame === 0 }"
                            loading="eager"
                        ></iframe>
                        <iframe
                            x-ref="frameB"
                            title="Live preview da página"
                            class="pb-frame"
                            x-bind:class="{ 'is-active': activeFrame === 1 }"
                            loading="eager"
                        ></iframe>
                    </div>
                </div>

                <div class="pb-loading-bar" x-show="status === 'loading'" x-transition.opacity></div>

                <div class="pb-viewport-error" x-show="status === 'error'" x-cloak>
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" />
                    <span>Não foi possível atualizar o preview.</span>
                    <button type="button" x-on:click="refresh()">Tentar novamente</button>
                </div>
            </div>
        </section>

        {{-- ───────────── Coluna 3: Estrutura (itens do builder) ───────────── --}}
        <aside class="pb-col pb-col--layers" aria-label="Estrutura da página">
            <header class="pb-col-header">
                <div>
                    <h3 class="pb-col-title">
                        Estrutura
                        <span class="pb-count">{{ count($containers) }}</span>
                    </h3>
                    <p class="pb-col-subtitle">Arraste para reordenar · clique para editar</p>
                </div>

                @if ($hasTemplates)
                    <button type="button" id="page-builder-templates" class="pb-template-btn" x-on:click="{{ $openTemplates }}" title="Carregar um template de página">
                        <x-filament::icon icon="heroicon-m-sparkles" />
                        <span>Templates</span>
                    </button>
                @endif
            </header>

            @if (count($containers))
                <ul
                    class="pb-layers"
                    x-sortable
                    data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                    wire:end.stop="{{ 'mountFormComponentAction(\'' . $statePath . '\', \'' . $reorderAction->getName() . '\', { items: $event.target.sortable.toArray() })' }}"
                >
                    @foreach ($containers as $uuid => $item)
                        @php
                            $itemBlock = $item->getParentComponent();
                            $itemState = $item->getRawState();
                            $itemLabel = (string) $itemBlock->getLabel($itemState, $uuid);
                            $itemIcon = $itemBlock->getIcon($itemState, $uuid) ?? 'heroicon-o-square-3-stack-3d';

                            $itemSubtitle = collect(['title', 'heading', 'badge', 'label', 'name', 'subtitle', 'description'])
                                ->map(fn ($key) => data_get($itemState, $key))
                                ->first(fn ($value) => is_string($value) && filled(trim(strip_tags($value))));
                            $itemSubtitle = $itemSubtitle ? Str::limit(trim(strip_tags($itemSubtitle)), 48) : null;

                            $visibleExtraItemActions = array_filter(
                                $extraItemActions,
                                fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                            );
                            $itemCloneAction = $cloneAction(['item' => $uuid]);
                            $itemCloneVisible = $isCloneable && $itemCloneAction->isVisible();
                            $itemDeleteAction = $deleteAction(['item' => $uuid]);
                            $itemDeleteVisible = $isDeletable && $itemDeleteAction->isVisible();
                            $itemMoveDownAction = $moveDownAction(['item' => $uuid])->disabled($loop->last);
                            $itemMoveUpAction = $moveUpAction(['item' => $uuid])->disabled($loop->first);
                            $itemMoveVisible = $isReorderableWithButtons && ($itemMoveUpAction->isVisible() || $itemMoveDownAction->isVisible());
                            $itemReorderVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                        @endphp

                        <li
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.pb-item"
                            x-sortable-item="{{ $uuid }}"
                            id="page-builder-layer-{{ $uuid }}"
                            class="pb-layer"
                            x-bind:class="{ 'is-hovered': hovered === @js($uuid), 'is-selected': selected === @js($uuid) }"
                            x-on:mouseenter="highlight(@js($uuid))"
                            x-on:mouseleave="highlight(null)"
                            x-on:click="editBlock(@js($uuid))"
                        >
                            @if ($itemReorderVisible)
                                <span class="pb-layer-handle" x-sortable-handle x-on:click.stop title="Arrastar">
                                    {{ $reorderAction }}
                                </span>
                            @endif

                            <span class="pb-layer-icon">
                                <x-filament::icon :icon="$itemIcon" />
                                <span class="pb-layer-index">{{ $loop->iteration }}</span>
                            </span>

                            <span class="pb-layer-text">
                                <span class="pb-layer-title">{{ $itemLabel }}</span>
                                @if ($itemSubtitle)
                                    <span class="pb-layer-sub">{{ $itemSubtitle }}</span>
                                @endif
                            </span>

                            <span class="pb-layer-actions" x-on:click.stop>
                                @foreach ($visibleExtraItemActions as $extraItemAction)
                                    {{ $extraItemAction(['item' => $uuid]) }}
                                @endforeach

                                @if ($itemMoveVisible)
                                    {{ $itemMoveUpAction }}
                                    {{ $itemMoveDownAction }}
                                @endif

                                <button type="button" class="pb-layer-locate" title="Localizar no preview" x-on:click="locate(@js($uuid))">
                                    <x-filament::icon icon="heroicon-m-viewfinder-circle" />
                                </button>

                                @if ($itemCloneVisible)
                                    {{ $itemCloneAction }}
                                @endif

                                @if ($itemDeleteVisible)
                                    {{ $itemDeleteAction }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="pb-layers-empty">
                    <div class="pb-layers-empty-icon">
                        <x-filament::icon icon="heroicon-o-squares-plus" />
                    </div>
                    <p class="pb-layers-empty-title">Nenhum bloco ainda</p>
                    <p class="pb-layers-empty-text">Escolha um bloco na biblioteca à esquerda ou comece por uma página pronta.</p>

                    @if ($hasTemplates)
                        <button type="button" id="page-builder-templates-empty" class="pb-template-cta" x-on:click="{{ $openTemplates }}">
                            <x-filament::icon icon="heroicon-m-sparkles" />
                            <span>Escolher um template</span>
                        </button>
                    @endif
                </div>
            @endif
        </aside>
    </div>
</x-dynamic-component>
