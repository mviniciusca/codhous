{{--
    Editor de páginas (Create/Edit): cabeçalho compacto com abas, status de rascunho,
    auto save e aviso de alterações não salvas. Substitui as views padrão do Filament.
--}}
@php
    $record = method_exists($this, 'getRecord') ? $this->getRecord() : null;
    $isCreate = ! $record;
    $submitMethod = $isCreate ? 'create' : 'save';

    $editorConfig = array_merge($this->getEditorConfig(), [
        'isCreate' => $isCreate,
        'isVisible' => (bool) $record?->is_visible,
        'everPublished' => (bool) $record?->published_at,
    ]);
@endphp

<x-filament-panels::page @class([
    'pe-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    $isCreate ? 'fi-resource-create-record-page' : 'fi-resource-edit-record-page',
])>
    @include('filament.page-builder.editor-styles')

    <script>
        window.pageEditor = window.pageEditor || function (config) {
            return {
                config,
                tab: 'content',
                localDirty: false,
                saving: false,
                failed: false,
                lastInputAt: 0,
                _timer: null,
                _firstDirtyAt: 0,

                init() {
                    if (window.location.hash === '#configuracoes') this.tab = 'settings';

                    // Qualquer requisição deste componente: marca início (para não perder
                    // digitação feita durante o envio) e, ao terminar, reavalia o auto save.
                    const unhook = window.Livewire.hook('commit', ({ component, succeed }) => {
                        if (component.id !== this.$wire.id) return;
                        const startedAt = Date.now();

                        succeed(() => queueMicrotask(() => {
                            if (this.lastInputAt <= startedAt) this.localDirty = false;
                            this.scheduleAutosave();
                        }));
                    });

                    const isEditorField = (target) => target instanceof Element
                        && !target.closest('.pe-header, #page-builder-block-search')
                        && !!target.closest('.pe-editor form, .fi-modal');

                    const onInput = (event) => {
                        if (!isEditorField(event.target)) return;
                        this.lastInputAt = Date.now();
                        this.localDirty = true;
                        this.scheduleAutosave();
                    };

                    const onKeydown = (event) => {
                        if ((event.ctrlKey || event.metaKey) && !event.shiftKey && event.key.toLowerCase() === 's') {
                            event.preventDefault();
                            this.saveNow();
                        }
                    };

                    const onBeforeUnload = (event) => {
                        if (this.$wire?.__instance?.effects?.redirect) return;
                        if (!this.dirty && !this.saving) return;
                        event.preventDefault();
                        event.returnValue = true;
                    };

                    ['input', 'change'].forEach((name) => document.addEventListener(name, onInput, true));
                    document.addEventListener('keydown', onKeydown);
                    window.addEventListener('beforeunload', onBeforeUnload);

                    this.destroy = () => {
                        if (typeof unhook === 'function') unhook();
                        ['input', 'change'].forEach((name) => document.removeEventListener(name, onInput, true));
                        document.removeEventListener('keydown', onKeydown);
                        window.removeEventListener('beforeunload', onBeforeUnload);
                        clearTimeout(this._timer);
                    };
                },

                get dirty() {
                    return this.localDirty || !!this.$wire.hasPendingChanges;
                },

                get status() {
                    if (this.saving) return 'saving';
                    if (this.failed) return 'error';
                    if (this.dirty) return 'dirty';
                    if (this.config.isCreate) return 'new';
                    if (this.$wire.draftSavedAt) return 'draft';
                    return this.config.isVisible ? 'published' : 'hidden';
                },

                get statusLabel() {
                    switch (this.status) {
                        case 'saving': return 'Salvando…';
                        case 'error': return 'Erro ao salvar';
                        case 'dirty': return this.config.autosave ? 'Alterações não salvas' : 'Não salvo';
                        case 'new': return 'Nova página';
                        case 'draft': return 'Rascunho · ' + this.formatTime(this.$wire.draftSavedAt);
                        case 'hidden': return 'Oculta do site';
                        default: return 'Publicada';
                    }
                },

                get statusHint() {
                    switch (this.status) {
                        case 'dirty': return this.config.autosave
                            ? 'O rascunho será salvo automaticamente em instantes (Ctrl+S para salvar agora).'
                            : 'Salve como rascunho (Ctrl+S) ou publique para não perder as alterações.';
                        case 'draft': return this.config.everPublished
                            ? 'Há alterações salvas que ainda não estão no site. Clique em "Publicar".'
                            : 'Esta página ainda não foi publicada.';
                        case 'error': return 'Não foi possível salvar o rascunho. Tente novamente com Ctrl+S.';
                        case 'hidden': return 'A página está publicada, mas oculta para os visitantes.';
                        default: return '';
                    }
                },

                get displaySlug() {
                    const slug = (this.$wire.data?.slug || '').replace(/^\/+/, '');
                    return '/' + slug;
                },

                setTab(tab) {
                    this.tab = tab === 'settings' ? 'settings' : 'content';
                    history.replaceState(null, '', this.tab === 'settings' ? '#configuracoes' : window.location.pathname + window.location.search);
                },

                scheduleAutosave() {
                    if (!this.config.autosave || !this.dirty) return;

                    const now = Date.now();
                    if (!this._firstDirtyAt) this._firstDirtyAt = now;

                    // Debounce, mas sem passar de 15s com alterações pendentes
                    const wait = Math.max(0, Math.min(this.config.autosaveDelay, this._firstDirtyAt + 15000 - now));

                    clearTimeout(this._timer);
                    this._timer = setTimeout(() => this.autosave(), wait);
                },

                async autosave() {
                    if (!this.dirty || this.saving) return;
                    await this.persist(true);
                },

                async saveNow() {
                    if (this.saving) return;
                    await this.persist(false);
                },

                async persist(silent) {
                    clearTimeout(this._timer);
                    this.saving = true;
                    this.failed = false;
                    const startedAt = Date.now();

                    try {
                        await this.$wire.saveDraft(silent);
                        if (this.lastInputAt <= startedAt) this.localDirty = false;
                        this._firstDirtyAt = 0;
                    } catch (error) {
                        this.failed = true;
                    } finally {
                        this.saving = false;
                    }

                    if (this.dirty) this.scheduleAutosave();
                },

                formatTime(iso) {
                    if (!iso) return '';
                    const date = new Date(iso);
                    const sameDay = date.toDateString() === new Date().toDateString();
                    const time = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    return sameDay ? 'salvo às ' + time : 'salvo em ' + date.toLocaleDateString('pt-BR') + ' ' + time;
                },
            };
        };
    </script>

    <div
        class="pe-editor"
        x-data="pageEditor(@js($editorConfig))"
        x-on:page-editor-tab.window="setTab($event.detail.tab)"
    >
        <header class="pe-header">
            <div class="pe-identity">
                <a href="{{ $this->getResource()::getUrl('index') }}" class="pe-back" title="Voltar para Páginas" aria-label="Voltar para Páginas">
                    <x-filament::icon icon="heroicon-m-arrow-left" />
                </a>

                <div class="pe-titles">
                    <div class="pe-title-row">
                        <h1 class="pe-title" x-text="$wire.data?.title || 'Nova página'">{{ $record?->title ?? 'Nova página' }}</h1>

                        <span class="pe-status" x-bind:data-status="status" x-bind:title="statusHint" x-cloak>
                            <span class="pe-status-dot"></span>
                            <span x-text="statusLabel"></span>
                        </span>
                    </div>

                    <span class="pe-url">
                        <span class="pe-url-host">{{ preg_replace('#^https?://#', '', url('/')) }}</span><span x-text="displaySlug">{{ $record ? '/' . ltrim($record->slug, '/') : '/' }}</span>
                    </span>
                </div>
            </div>

            <nav class="pe-tabs" role="tablist" aria-label="Seções do editor">
                <button type="button" role="tab" id="page-editor-tab-content" class="pe-tab" x-bind:class="{ 'is-active': tab === 'content' }" x-bind:aria-selected="tab === 'content'" x-on:click="setTab('content')">
                    <x-filament::icon icon="heroicon-m-squares-2x2" />
                    <span>Conteúdo</span>
                </button>
                <button type="button" role="tab" id="page-editor-tab-settings" class="pe-tab" x-bind:class="{ 'is-active': tab === 'settings' }" x-bind:aria-selected="tab === 'settings'" x-on:click="setTab('settings')">
                    <x-filament::icon icon="heroicon-m-adjustments-horizontal" />
                    <span>Configurações e SEO</span>
                </button>
            </nav>

            <div class="pe-actions">
                <x-filament::actions :actions="$this->getCachedHeaderActions()" />
            </div>
        </header>

        <x-filament-panels::form
            id="form"
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="{{ $submitMethod }}"
        >
            {{ $this->form }}
        </x-filament-panels::form>
    </div>

</x-filament-panels::page>
