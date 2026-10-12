{{-- Alpine component da prévia do editor de alertas --}}
@once
<script>
    window.alertEditorPreview = window.alertEditorPreview || function (config) {
        // Tela de referência de cada dispositivo (o site é responsivo dentro do iframe)
        const DEVICES = { desktop: { w: 1280, h: 800 }, mobile: { w: 390, h: 844 } };
        const MOBILE_MAX_HEIGHT = 560;
        const PADDING = 16;

        const remember = (key, value) => {
            try {
                if (value === undefined) return localStorage.getItem(key);
                localStorage.setItem(key, value);
            } catch (e) {}
            return null;
        };

        return {
            device: remember('al-preview-device') === 'mobile' ? 'mobile' : 'desktop',
            frameSrc: config.src,
            scale: 1,
            ready: false,
            status: 'loading',
            hint: '',
            expanded: false,
            _inflight: false,
            _pending: false,
            _timer: null,
            _hintTimer: null,
            _cleanup: [],

            init() {
                const observer = new ResizeObserver(() => this.measure());
                observer.observe(this.$refs.stage);
                this._cleanup.push(() => observer.disconnect());

                // Campos live, toggles e botões do formulário: atualiza depois da resposta do servidor
                const unhook = window.Livewire.hook('commit', ({ component, commit, succeed }) => {
                    if (component.id !== this.$wire.id) return;

                    const calls = (commit.calls || []).map((call) => call.method);
                    if (calls.length > 0 && calls.every((method) => method === 'renderAlertPreview')) return;

                    succeed(() => queueMicrotask(() => this.queue(120)));
                });
                if (typeof unhook === 'function') this._cleanup.push(unhook);

                // Digitação nos campos de texto (não live): atualização com debounce
                const onInput = (event) => {
                    if (event.target instanceof Element && event.target.closest('.al-editor')) this.queue(400);
                };
                document.addEventListener('input', onInput, true);
                this._cleanup.push(() => document.removeEventListener('input', onInput, true));

                const onMessage = (event) => this.onFrameMessage(event);
                window.addEventListener('message', onMessage);
                this._cleanup.push(() => window.removeEventListener('message', onMessage));

                this.$nextTick(() => this.measure());
            },

            destroy() {
                this._cleanup.forEach((fn) => { try { fn(); } catch (e) {} });
                clearTimeout(this._timer);
                clearTimeout(this._hintTimer);
                document.documentElement.classList.remove('al-noscroll');
            },

            get statusLabel() {
                return { loading: 'Atualizando…', ready: 'Atualizado', error: 'Não foi possível atualizar' }[this.status] || '';
            },

            get outerStyle() {
                const { w, h } = DEVICES[this.device];
                return `width:${Math.round(w * this.scale)}px;height:${Math.round(h * this.scale)}px;`;
            },

            get innerStyle() {
                const { w, h } = DEVICES[this.device];
                return `width:${w}px;height:${h}px;transform:scale(${this.scale});`;
            },

            get stageStyle() {
                // Expandida, a área ocupa a tela e o site se ajusta a ela
                return this.expanded ? '' : `height:${Math.round(DEVICES[this.device].h * this.scale) + PADDING * 2}px;`;
            },

            measure() {
                const stage = this.$refs.stage;
                if (!stage) return;

                const available = Math.max(stage.clientWidth - PADDING * 2, 160);
                const { w, h } = DEVICES[this.device];

                if (this.expanded) {
                    this.scale = Math.min(1, available / w, Math.max(stage.clientHeight - PADDING * 2, 160) / h);
                    return;
                }

                this.scale = this.device === 'mobile'
                    ? Math.min(1, available / w, MOBILE_MAX_HEIGHT / h)
                    : Math.min(1, available / w);
            },

            toggleExpanded() {
                this.expanded = !this.expanded;
                document.documentElement.classList.toggle('al-noscroll', this.expanded);
                this.$nextTick(() => this.measure());
            },

            setDevice(device) {
                if (!DEVICES[device]) return;
                this.device = device;
                remember('al-preview-device', device);
                this.$nextTick(() => this.measure());
            },

            queue(delay = 300) {
                clearTimeout(this._timer);
                this._timer = setTimeout(() => this.push(), delay);
            },

            // Pede ao servidor o HTML do alerta (componente real do site) e envia ao iframe
            async push() {
                clearTimeout(this._timer);
                if (!this.ready) return;

                if (this._inflight) {
                    this._pending = true;
                    return;
                }

                this._inflight = true;
                this.status = 'loading';

                try {
                    const html = await this.$wire.renderAlertPreview();
                    this.$refs.frame.contentWindow.postMessage({ source: 'codhous-panel', type: 'alert', html: String(html || '') }, window.location.origin);
                    this.status = 'ready';
                } catch (error) {
                    console.error('[Alertas] Prévia:', error);
                    this.status = 'error';
                } finally {
                    this._inflight = false;
                    if (this._pending) {
                        this._pending = false;
                        this.queue(50);
                    }
                }
            },

            onFrameMessage(event) {
                if (event.origin !== window.location.origin || event.source !== this.$refs.frame?.contentWindow) return;

                const data = event.data || {};
                if (data.source !== 'codhous-preview') return;

                if (data.type === 'ready') {
                    this.ready = true;
                    this.push();
                } else if (data.type === 'navigate' && typeof data.path === 'string') {
                    // Links internos do site abrem a outra página dentro da prévia
                    this.ready = false;
                    this.frameSrc = config.src + '?pagina=' + encodeURIComponent(data.path);
                } else if (data.type === 'alert-cta') {
                    this.showHint(`Botão de demonstração. No site, “${data.label || 'o botão'}” abre ${data.href}.`);
                } else if (data.type === 'alert-dismissed') {
                    this.showHint('Alerta fechado, como o visitante faria. Use “Mostrar de novo” para vê-lo outra vez.');
                } else if (data.type === 'blocked') {
                    this.showHint(data.form ? 'Formulários não são enviados na prévia.' : 'Links externos não abrem na prévia.');
                }
            },

            showHint(text) {
                this.hint = text;
                clearTimeout(this._hintTimer);
                this._hintTimer = setTimeout(() => (this.hint = ''), 4000);
            },
        };
    };
</script>
@endonce
