{{-- Alpine component do Page Builder com Live Preview --}}
<script>
    window.pageBuilderLivePreview = window.pageBuilderLivePreview || function (config) {
        const DEVICES = {
            desktop: { width: 1440, label: 'Desktop' },
            tablet: { width: 834, label: 'Tablet' },
            mobile: { width: 390, label: 'Celular' },
        };

        return {
            config,
            device: localStorage.getItem('pb-device') || 'desktop',
            status: 'idle',
            url: null,
            activeFrame: 0,
            scale: 1,
            viewportW: 0,
            viewportH: 0,
            hovered: null,
            selected: null,
            isEditing: false,
            paletteQuery: '',
            pendingScroll: null,
            _inflight: false,
            _pending: false,
            _timer: null,
            _cleanup: [],

            init() {
                if (!DEVICES[this.device]) this.device = 'desktop';

                // Recalcula escala do iframe quando a coluna muda de tamanho
                const observer = new ResizeObserver(() => this.measure());
                observer.observe(this.$refs.viewport);
                this._cleanup.push(() => observer.disconnect());
                this.measure();

                // Qualquer requisição do Livewire deste componente (adicionar, remover,
                // reordenar, salvar edição...) dispara a atualização do preview.
                const unhook = window.Livewire.hook('commit', ({ component, commit, succeed }) => {
                    if (component.id !== this.$wire.id) return;

                    const calls = (commit.calls || []).map((call) => call.method);
                    const isOwn = calls.length > 0 && calls.every((method) => ['generateLivePreviewUrl', 'saveDraft'].includes(method));

                    succeed(() => {
                        queueMicrotask(() => {
                            this.syncEditing();
                            if (!isOwn) this.queueRefresh(200);
                        });
                    });
                });
                if (typeof unhook === 'function') this._cleanup.push(unhook);

                // Digitação no painel de edição → atualização ao vivo (debounced)
                const onInput = (event) => {
                    const target = event.target;
                    if (!(target instanceof Element)) return;
                    if (this.$root.contains(target)) return;
                    if (!target.closest('.pb-inspector, .fi-fo-component-ctn, form')) return;
                    this.queueRefresh(650);
                };
                const onClick = (event) => {
                    const target = event.target;
                    if (!(target instanceof Element)) return;
                    if (!target.closest('.pb-inspector')) return;
                    if (!target.closest('[role="switch"], input[type="checkbox"], input[type="radio"], [role="option"], [role="tab"]')) return;
                    this.queueRefresh(650);
                };
                ['input', 'change', 'trix-change'].forEach((name) => document.addEventListener(name, onInput, true));
                document.addEventListener('click', onClick, true);
                this._cleanup.push(() => {
                    ['input', 'change', 'trix-change'].forEach((name) => document.removeEventListener(name, onInput, true));
                    document.removeEventListener('click', onClick, true);
                });

                // Mensagens vindas do iframe
                const onMessage = (event) => this.onFrameMessage(event);
                window.addEventListener('message', onMessage);
                this._cleanup.push(() => window.removeEventListener('message', onMessage));

                this.$nextTick(() => {
                    this.syncEditing();
                    this.refresh();
                });
            },

            destroy() {
                this._cleanup.forEach((fn) => { try { fn(); } catch (e) {} });
                clearTimeout(this._timer);
            },

            // ───────── Estado ─────────
            get statusLabel() {
                return {
                    idle: 'Preparando…',
                    loading: 'Atualizando…',
                    ready: 'Ao vivo',
                    error: 'Erro no preview',
                }[this.status] || '';
            },

            get frameOuterStyle() {
                const width = DEVICES[this.device].width;
                return `width:${Math.round(width * this.scale)}px;height:${Math.max(this.viewportH, 0)}px;`;
            },

            get frameInnerStyle() {
                const width = DEVICES[this.device].width;
                const height = this.scale > 0 ? this.viewportH / this.scale : this.viewportH;
                return `width:${width}px;height:${Math.round(height)}px;transform:scale(${this.scale});`;
            },

            matches(label) {
                const query = (this.paletteQuery || '').trim().toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                return !query || label.includes(query);
            },

            measure() {
                const viewport = this.$refs.viewport;
                if (!viewport) return;
                const gutter = this.device === 'desktop' ? 24 : 48;
                this.viewportW = Math.max(viewport.clientWidth - gutter, 200);
                this.viewportH = Math.max(viewport.clientHeight - gutter, 200);
                this.scale = Math.min(1, this.viewportW / DEVICES[this.device].width);
            },

            setDevice(device) {
                if (!DEVICES[device]) return;
                this.device = device;
                localStorage.setItem('pb-device', device);
                this.$nextTick(() => this.measure());
            },

            syncEditing() {
                const list = (value) => Array.isArray(value) ? value : Object.values(value || {});
                const names = list(this.$wire.mountedFormComponentActions);
                const components = list(this.$wire.mountedFormComponentActionsComponents);
                const args = list(this.$wire.mountedFormComponentActionsArguments);

                let uuid = null;
                names.forEach((name, index) => {
                    if (name === this.config.editAction && components[index] === this.config.statePath) {
                        uuid = (args[index] || {}).item || uuid;
                    }
                });

                const changed = uuid !== this.selected;
                this.isEditing = names.length > 0;
                this.selected = uuid;

                if (changed) this.post({ type: 'select', uuid, scroll: !!uuid });
            },

            // ───────── Preview ─────────
            queueRefresh(delay = 300) {
                clearTimeout(this._timer);
                this._timer = setTimeout(() => this.refresh(), delay);
            },

            async refresh() {
                clearTimeout(this._timer);

                if (this._inflight) {
                    this._pending = true;
                    return;
                }

                this._inflight = true;
                this.status = 'loading';

                try {
                    const url = await this.$wire.generateLivePreviewUrl();
                    if (!url) throw new Error('Preview indisponível');
                    this.url = url;
                    await this.load(url);
                    this.status = 'ready';
                } catch (error) {
                    console.error('[Page Builder] Live preview:', error);
                    this.status = 'error';
                } finally {
                    this._inflight = false;
                    if (this._pending) {
                        this._pending = false;
                        this.queueRefresh(50);
                    }
                }
            },

            // Double-buffer: carrega no iframe oculto, restaura o scroll e troca (sem piscar)
            load(url) {
                return new Promise((resolve, reject) => {
                    const frames = [this.$refs.frameA, this.$refs.frameB];
                    const current = frames[this.activeFrame];
                    const next = frames[1 - this.activeFrame];

                    let scrollY = 0;
                    try { scrollY = current.contentWindow ? current.contentWindow.scrollY : 0; } catch (e) {}

                    const timeout = setTimeout(() => {
                        next.onload = null;
                        reject(new Error('Tempo esgotado ao carregar o preview'));
                    }, 20000);

                    next.onload = () => {
                        clearTimeout(timeout);
                        next.onload = null;

                        try { next.contentWindow.scrollTo({ top: scrollY, behavior: 'instant' }); } catch (e) {}

                        requestAnimationFrame(() => {
                            this.activeFrame = 1 - this.activeFrame;

                            this.post({ type: 'select', uuid: this.selected, scroll: false });
                            if (this.hovered) this.post({ type: 'highlight', uuid: this.hovered });
                            if (this.pendingScroll) {
                                this.post({ type: 'scroll', uuid: this.pendingScroll });
                                this.pendingScroll = null;
                            }

                            resolve();
                        });
                    };

                    next.src = url + (url.includes('?') ? '&' : '?') + 'v=' + Date.now();
                });
            },

            activeWindow() {
                const frame = [this.$refs.frameA, this.$refs.frameB][this.activeFrame];
                return frame ? frame.contentWindow : null;
            },

            post(message) {
                const win = this.activeWindow();
                if (!win) return;
                try { win.postMessage({ ...message, source: 'pb-host' }, window.location.origin); } catch (e) {}
            },

            onFrameMessage(event) {
                if (event.origin !== window.location.origin) return;
                const data = event.data || {};
                if (data.source !== 'pb-preview') return;
                if (event.source !== this.activeWindow()) return;

                if (data.type === 'hover') this.hovered = data.uuid || null;
                if (data.type === 'select' && data.uuid) this.editBlock(data.uuid, { scroll: false });
            },

            // ───────── Ações ─────────
            highlight(uuid) {
                this.hovered = uuid;
                this.post({ type: 'highlight', uuid });
            },

            locate(uuid) {
                this.post({ type: 'scroll', uuid });
                this.post({ type: 'highlight', uuid });
            },

            editBlock(uuid, { scroll = true } = {}) {
                if (this.isEditing) {
                    if (uuid !== this.selected && window.FilamentNotification) {
                        new window.FilamentNotification()
                            .title('Finalize a edição atual')
                            .body('Clique em "Aplicar" ou "Cancelar" antes de editar outro bloco.')
                            .warning()
                            .send();
                    }
                    return;
                }

                this.selected = uuid;
                this.post({ type: 'select', uuid, scroll });
                this.$wire.mountFormComponentAction(this.config.statePath, this.config.editAction, { item: uuid });
            },

            addBlock(name) {
                if (this.isEditing) return;
                this.$wire.mountFormComponentAction(this.config.statePath, this.config.addAction, { block: name });
            },

            onBlockAdded(detail) {
                const uuid = detail && (detail.uuid || (Array.isArray(detail) ? detail[0]?.uuid : null));
                if (!uuid) return;
                this.pendingScroll = uuid;
                // Abre o painel de edição do bloco recém-criado
                setTimeout(() => {
                    this.isEditing = false;
                    this.editBlock(uuid, { scroll: true });
                }, 50);
            },
        };
    };
</script>
