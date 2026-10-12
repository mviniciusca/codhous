<x-filament-panels::page class="fi-settings-page">
    @include('filament.settings.styles')

    <div
        class="st cx-ui"
        x-data="{
            dirty: false,
            snapshot: '',
            serialize() { return JSON.stringify($wire.data ?? {}) },
            markSaved() {
                this.snapshot = this.serialize()
                this.dirty = false
                document.documentElement.classList.remove('st-dirty')
            },
            init() {
                this.markSaved()
                $wire.$watch('data', () => {
                    this.dirty = this.serialize() !== this.snapshot
                    document.documentElement.classList.toggle('st-dirty', this.dirty)
                })
                window.addEventListener('beforeunload', (event) => {
                    if (this.dirty) { event.preventDefault(); event.returnValue = '' }
                })
            },
        }"
        x-on:settings-saved.window="$nextTick(() => markSaved())"
    >
        <form id="form" wire:submit="save" class="st-form">
            {{ $this->form }}
        </form>

        <footer class="st-foot" aria-live="polite">
            <span class="st-state" x-bind:class="dirty ? 'is-dirty' : 'is-saved'">
                <span class="cx-dot" aria-hidden="true"></span>
                <span x-text="dirty ? 'Alterações não salvas' : 'Tudo salvo'"></span>
            </span>
            <span class="st-hint"><kbd>Ctrl</kbd> <kbd>S</kbd> salva</span>
        </footer>
    </div>
</x-filament-panels::page>
