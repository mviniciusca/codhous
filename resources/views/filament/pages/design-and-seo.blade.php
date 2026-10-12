<x-filament-panels::page class="fi-design-page">
    @include('filament.design.styles')

    <script>
        // Estado da tela: alterações pendentes e prévia em dois iframes alternados (sem piscar)
        window.designStudio = (config) => ({
            devices: { desktop: 1280, tablet: 820, mobile: 390 },
            device: 'desktop',
            path: '/',
            mode: 'edit',
            active: 'a',
            loading: false,
            failed: false,
            notice: null,
            dirty: false,
            scale: 1,
            stageHeight: 0,
            snapshot: '',
            lastRendered: '',
            lastColor: null,
            sequence: 0,
            timer: null,

            init() {
                try { this.device = localStorage.getItem('ds-device') || 'desktop' } catch (e) {}
                this.$watch('device', (value) => {
                    try { localStorage.setItem('ds-device', value) } catch (e) {}
                    this.$nextTick(() => this.fit())
                })

                this.markSaved()
                this.lastColor = this.color()

                new ResizeObserver(() => this.fit()).observe(this.$refs.stage)
                this.fit()

                this.$wire.$watch('data', () => this.changed())

                window.addEventListener('message', (event) => this.receive(event))
                window.addEventListener('beforeunload', (event) => {
                    if (this.dirty) { event.preventDefault(); event.returnValue = '' }
                })

                this.refresh()
            },

            // Estado sem a cor: a cor é aplicada direto no iframe, sem recarregar
            serialize(withColor = true) {
                const data = JSON.parse(JSON.stringify(this.$wire.data ?? {}))
                const widget = data.settings?.website?.features?.whatsapp_widget
                // A máscara reformata o número ao carregar; só os dígitos importam
                if (widget?.number) widget.number = String(widget.number).replace(/\D/g, '')
                if (! withColor && data.settings?.website) delete data.settings.website.primary_color
                return JSON.stringify(data)
            },

            color() { return this.$wire.data?.settings?.website?.primary_color ?? null },

            markSaved() {
                this.snapshot = this.serialize()
                this.dirty = false
                document.documentElement.classList.remove('ds-dirty')
            },

            changed() {
                this.dirty = this.serialize() !== this.snapshot
                document.documentElement.classList.toggle('ds-dirty', this.dirty)

                if (this.color() !== this.lastColor) {
                    this.lastColor = this.color()
                    this.post({ type: 'color', value: this.lastColor })
                }

                if (this.serialize(false) === this.lastRendered) return

                clearTimeout(this.timer)
                this.timer = setTimeout(() => this.refresh(), 700)
            },

            async refresh(path = null) {
                if (path) this.path = path
                clearTimeout(this.timer)

                const sequence = ++this.sequence
                const rendered = this.serialize(false)
                this.loading = true
                this.failed = false

                let url
                try {
                    url = await this.$wire.previewUrl(this.path)
                } catch (e) {
                    url = null
                }

                if (sequence !== this.sequence) return
                if (! url) { this.loading = false; this.failed = true; return }

                const next = this.active === 'a' ? 'b' : 'a'
                const frame = this.$refs['frame_' + next]
                const current = this.$refs['frame_' + this.active]
                let scrollY = 0
                try { scrollY = current.contentWindow?.scrollY ?? 0 } catch (e) {}

                frame.onload = () => {
                    if (sequence !== this.sequence) return
                    // Mesma página: mantém a rolagem para a troca passar despercebida
                    try { if (! path) frame.contentWindow.scrollTo(0, scrollY) } catch (e) {}
                    this.active = next
                    this.loading = false
                    this.lastRendered = rendered
                    // Cor escolhida durante o carregamento
                    this.post({ type: 'color', value: this.color() })
                }
                frame.onerror = () => { if (sequence === this.sequence) { this.loading = false; this.failed = true } }
                frame.src = url
            },

            post(message) {
                try {
                    this.$refs['frame_' + this.active]?.contentWindow?.postMessage({ source: 'codhous-panel', ...message }, window.location.origin)
                } catch (e) {}
            },

            receive(event) {
                if (event.origin !== window.location.origin || event.data?.source !== 'codhous-preview') return

                if (event.data.type === 'navigate') this.refresh(event.data.path)
                if (event.data.type === 'blocked') this.flash(event.data.form ? 'Formulários não são enviados na prévia.' : 'Links externos ficam desativados na prévia.')
            },

            flash(text) {
                this.notice = text
                clearTimeout(this._notice)
                this._notice = setTimeout(() => this.notice = null, 3500)
            },

            fit() {
                const stage = this.$refs.stage
                if (! stage) return
                const width = this.devices[this.device]
                const available = stage.clientWidth - (this.device === 'desktop' ? 0 : 32)
                this.scale = Math.min(1, available / width)
                this.stageHeight = stage.clientHeight
            },

            get frameStyle() {
                const width = this.devices[this.device]
                const inset = this.device === 'desktop' ? 0 : 32
                return `width: ${width}px; height: ${Math.max(400, (this.stageHeight - inset) / this.scale)}px; transform: scale(${this.scale});`
            },

            get publicUrl() { return config.siteUrl + (this.path === '/' ? '' : this.path) },
        })
    </script>

    <div
        class="ds-root cx-ui"
        x-data="designStudio({ siteUrl: @js(rtrim(url('/'), '/')) })"
        x-bind:data-mode="mode"
        x-on:design-saved.window="$nextTick(() => markSaved())"
    >
    <div class="ds">
        {{-- Alternância em telas estreitas --}}
        <div class="ds-switch" role="tablist" aria-label="Modo de exibição">
            <button type="button" role="tab" x-bind:aria-selected="mode === 'edit'" x-on:click="mode = 'edit'">
                <x-filament::icon icon="heroicon-m-adjustments-horizontal" class="cx-icon-sm" /> Editar
            </button>
            <button type="button" role="tab" x-bind:aria-selected="mode === 'preview'" x-on:click="mode = 'preview'; $nextTick(() => fit())">
                <x-filament::icon icon="heroicon-m-eye" class="cx-icon-sm" /> Pré-visualizar
            </button>
        </div>

        {{-- Editor --}}
        <section class="ds-editor" aria-label="Configurações">
            <div class="ds-editor-scroll">
                <form wire:submit="save" class="ds-form">
                    {{ $this->form }}
                </form>
            </div>

            <footer class="ds-editor-foot" aria-live="polite">
                <span class="ds-state" x-bind:class="dirty ? 'is-dirty' : 'is-saved'">
                    <span class="cx-dot" aria-hidden="true"></span>
                    <span x-text="dirty ? 'Alterações não salvas' : 'Tudo salvo'"></span>
                </span>
                <span class="ds-hint"><kbd>Ctrl</kbd> <kbd>S</kbd> salva</span>
            </footer>
        </section>

        {{-- Prévia: wire:ignore mantém os iframes entre as atualizações do Livewire --}}
        <section class="ds-preview" aria-label="Pré-visualização do site" wire:ignore>
            <header class="ds-bar">
                <label class="ds-page">
                    <span class="cx-sr">Página</span>
                    <x-filament::icon icon="heroicon-m-document" class="cx-icon-sm" aria-hidden="true" />
                    <select x-model="path" x-on:change="refresh()">
                        @foreach ($this->getPreviewPages() as $page)
                            <option value="{{ $page['path'] }}">{{ $page['title'] }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="ds-devices" role="radiogroup" aria-label="Dispositivo">
                    @foreach (['desktop' => ['heroicon-m-computer-desktop', 'Desktop'], 'tablet' => ['heroicon-m-device-tablet', 'Tablet'], 'mobile' => ['heroicon-m-device-phone-mobile', 'Celular']] as $key => [$icon, $label])
                        <button
                            type="button"
                            role="radio"
                            x-on:click="device = '{{ $key }}'"
                            x-bind:aria-checked="device === '{{ $key }}'"
                            aria-label="{{ $label }}"
                            x-tooltip="{ content: '{{ $label }}', theme: $store.theme }"
                        >
                            <x-filament::icon :icon="$icon" class="cx-icon-sm" />
                        </button>
                    @endforeach
                </div>

                <div class="ds-bar-end">
                    <span class="ds-status" aria-live="polite">
                        <template x-if="loading">
                            <span class="ds-status-loading"><x-filament::loading-indicator class="cx-icon-sm" /> Atualizando</span>
                        </template>
                        <template x-if="! loading && failed">
                            <button type="button" class="ds-status-error" x-on:click="refresh()">Falha na prévia · tentar de novo</button>
                        </template>
                    </span>
                    <button type="button" class="ds-icon-btn" x-on:click="refresh()" aria-label="Recarregar prévia" x-tooltip="{ content: 'Recarregar', theme: $store.theme }">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="cx-icon-sm" />
                    </button>
                    <a class="ds-icon-btn" x-bind:href="publicUrl" target="_blank" rel="noopener" aria-label="Abrir versão publicada em nova aba" x-tooltip="{ content: 'Abrir site publicado', theme: $store.theme }">
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="cx-icon-sm" />
                    </a>
                </div>
            </header>

            <div class="ds-stage" x-ref="stage" x-bind:data-device="device">
                <div class="ds-frame" x-bind:style="frameStyle">
                    <iframe x-ref="frame_a" title="Pré-visualização do site" x-bind:class="{ 'is-active': active === 'a' }" x-bind:aria-hidden="active !== 'a'" x-bind:tabindex="active === 'a' ? 0 : -1"></iframe>
                    <iframe x-ref="frame_b" title="Pré-visualização do site" x-bind:class="{ 'is-active': active === 'b' }" x-bind:aria-hidden="active !== 'b'" x-bind:tabindex="active === 'b' ? 0 : -1"></iframe>
                </div>

                <p class="ds-notice" x-show="notice" x-transition.opacity x-text="notice" role="status"></p>
            </div>

            <footer class="ds-preview-foot">
                Mostra o site real com as alterações ainda não salvas. Scripts de terceiros não rodam aqui.
            </footer>
        </section>
    </div>
    </div>
</x-filament-panels::page>
