{{-- Alpine component do live preview do construtor de seções (baseado no do Page Builder) --}}
@once
<script>
    window.sectionBuilderPreview = window.sectionBuilderPreview || function (config) {
        // Tela de referência de cada dispositivo: unidades vh das seções se comportam como no site
        const DEVICES = { desktop: { w: 1440, h: 900 }, tablet: { w: 834, h: 1112 }, mobile: { w: 390, h: 844 } };
        const SKELETON = { width: 320, height: 170 };

        const remember = (key, value) => {
            try {
                if (value === undefined) return localStorage.getItem(key);
                localStorage.setItem(key, value);
            } catch (e) {}
            return null;
        };

        return {
            type: config.type,
            mode: remember('sb-mode') === 'structure' ? 'structure' : 'live',
            device: DEVICES[remember('sb-device')] ? remember('sb-device') : 'desktop',
            chrome: remember('sb-chrome') === '1',
            zoom: null,
            scale: 1,
            skeletonScale: 1,
            viewportW: 0,
            viewportH: 0,
            status: 'idle',
            url: null,
            activeFrame: 0,
            expanded: false,
            rendered: { empty: false, error: false },
            _loadingFrame: null,
            _inflight: false,
            _pending: false,
            _stale: true,
            _timer: null,
            _cleanup: [],

            init() {
                const observer = new ResizeObserver(() => this.measure());
                observer.observe(this.$refs.viewport);
                this._cleanup.push(() => observer.disconnect());

                // Qualquer requisição deste formulário (campos live, uploads, repeaters...) atualiza a prévia
                const unhook = window.Livewire.hook('commit', ({ component, commit, succeed }) => {
                    if (component.id !== this.$wire.id) return;

                    const calls = (commit.calls || []).map((call) => call.method);
                    const isOwn = calls.length > 0 && calls.every((method) => method === 'generateSectionPreviewUrl');

                    if (!isOwn) succeed(() => queueMicrotask(() => this.queueRefresh(200)));
                });
                if (typeof unhook === 'function') this._cleanup.push(unhook);

                // Digitação no inspetor (campos não-live) → atualização com debounce
                const inInspector = (target) => target instanceof Element && !!target.closest('.sb-col--inspector');
                const onInput = (event) => inInspector(event.target) && this.queueRefresh(650);
                const onClick = (event) => {
                    if (!inInspector(event.target)) return;
                    if (!event.target.closest('[role="switch"], input[type="checkbox"], input[type="radio"], [role="option"]')) return;
                    this.queueRefresh(650);
                };
                ['input', 'change', 'trix-change'].forEach((name) => document.addEventListener(name, onInput, true));
                document.addEventListener('click', onClick, true);
                this._cleanup.push(() => {
                    ['input', 'change', 'trix-change'].forEach((name) => document.removeEventListener(name, onInput, true));
                    document.removeEventListener('click', onClick, true);
                });

                const onMessage = (event) => this.onFrameMessage(event);
                window.addEventListener('message', onMessage);
                this._cleanup.push(() => window.removeEventListener('message', onMessage));

                this.$watch('type', () => {
                    this.rendered = { empty: false, error: false };
                    this.$nextTick(() => this.measure());
                });

                this.$nextTick(() => {
                    this.measure();
                    this.refresh();
                });
            },

            destroy() {
                this._cleanup.forEach((fn) => { try { fn(); } catch (e) {} });
                clearTimeout(this._timer);
            },

            // ───────── Estado ─────────
            get showSkeleton() {
                return this.mode === 'structure' || this.rendered.empty;
            },

            get statusLabel() {
                return { idle: 'Preparando…', loading: 'Atualizando…', ready: 'Atualizado', error: 'Erro' }[this.status] || '';
            },

            get frameOuterStyle() {
                const { w, h } = DEVICES[this.device];
                return `width:${Math.round(w * this.scale)}px;height:${Math.round(h * this.scale)}px;`;
            },

            get frameInnerStyle() {
                const { w, h } = DEVICES[this.device];
                return `width:${w}px;height:${h}px;transform:scale(${this.scale});`;
            },

            measure() {
                const viewport = this.$refs.viewport;
                if (!viewport) return;

                const gutter = this.device === 'desktop' ? 24 : 56;
                this.viewportW = Math.max(viewport.clientWidth - gutter, 200);
                this.viewportH = Math.max(viewport.clientHeight - gutter, 200);

                // Ajusta pela largura e pela altura: o dispositivo inteiro cabe na área
                const { w, h } = DEVICES[this.device];
                const fit = Math.min(1, this.viewportW / w, this.viewportH / h);
                this.scale = this.zoom ?? fit;

                this.skeletonScale = Math.max(.6, Math.min(
                    (viewport.clientWidth - 64) / SKELETON.width,
                    (viewport.clientHeight - 120) / SKELETON.height,
                    3.2,
                ));
            },

            zoomBy(step) {
                this.zoom = Math.min(1, Math.max(.2, Math.round((this.scale + step) * 10) / 10));
                this.$refs.viewport.classList.toggle('is-zoomed', this.zoom !== null);
                this.measure();
            },

            setDevice(device) {
                if (!DEVICES[device]) return;
                this.device = device;
                this.zoom = null;
                remember('sb-device', device);
                this.$nextTick(() => this.measure());
            },

            setMode(mode) {
                this.mode = mode;
                remember('sb-mode', mode);
                this.$nextTick(() => this.measure());
                if (mode === 'live' && this._stale) this.refresh();
            },

            toggleChrome() {
                this.chrome = !this.chrome;
                remember('sb-chrome', this.chrome ? '1' : '0');
                this.refresh();
            },

            toggleExpanded() {
                this.expanded = !this.expanded;
                document.documentElement.classList.toggle('sb-noscroll', this.expanded);
                this.$nextTick(() => this.measure());
            },

            // ───────── Preview ─────────
            queueRefresh(delay = 300) {
                this._stale = true;
                clearTimeout(this._timer);
                this._timer = setTimeout(() => this.refresh(), delay);
            },

            async refresh() {
                clearTimeout(this._timer);

                // Sem tipo ou no modo estrutura não há o que buscar; atualiza ao voltar para "Ao vivo"
                if (!this.type || this.mode !== 'live') {
                    this._stale = true;
                    return;
                }

                if (this._inflight) {
                    this._pending = true;
                    return;
                }

                this._inflight = true;
                this._stale = false;
                this.status = 'loading';

                try {
                    const url = await this.$wire.generateSectionPreviewUrl(this.chrome);
                    if (!url) throw new Error('Preview indisponível');
                    this.url = url;
                    await this.load(url);
                    this.status = 'ready';
                } catch (error) {
                    console.error('[Seções] Live preview:', error);
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
                    const nextIndex = 1 - this.activeFrame;
                    const next = frames[nextIndex];

                    let scrollY = 0;
                    try { scrollY = current.contentWindow ? current.contentWindow.scrollY : 0; } catch (e) {}

                    const timeout = setTimeout(() => {
                        next.onload = null;
                        reject(new Error('Tempo esgotado ao carregar a prévia'));
                    }, 20000);

                    this._loadingFrame = nextIndex;

                    next.onload = () => {
                        clearTimeout(timeout);
                        next.onload = null;

                        try { next.contentWindow.scrollTo({ top: scrollY, behavior: 'instant' }); } catch (e) {}

                        requestAnimationFrame(() => {
                            this.activeFrame = nextIndex;
                            resolve();
                        });
                    };

                    next.src = url + (url.includes('?') ? '&' : '?') + 'v=' + Date.now();
                });
            },

            onFrameMessage(event) {
                if (event.origin !== window.location.origin) return;

                const data = event.data || {};
                if (data.source !== 'sb-preview' || data.type !== 'rendered') return;

                const frames = [this.$refs.frameA, this.$refs.frameB];
                const latest = frames[this._loadingFrame ?? this.activeFrame];
                if (!latest || event.source !== latest.contentWindow) return;

                this.rendered = { empty: !!data.empty, error: !!data.error };
            },
        };
    };
</script>
@endonce
