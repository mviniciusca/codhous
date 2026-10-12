@php
    $options = $getOptions();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            options: @js(array_keys($options)),
            labels: @js($options),
            move(step) {
                const index = this.options.indexOf(this.state);
                this.state = this.options[(index + step + this.options.length) % this.options.length];
                this.$nextTick(() => this.$root.querySelector('[aria-checked=true]')?.focus());
            },
        }"
        class="cs"
    >
        <div
            role="radiogroup"
            aria-label="{{ $getLabel() }}"
            class="cs-grid"
            x-on:keydown.right.prevent="move(1)"
            x-on:keydown.down.prevent="move(1)"
            x-on:keydown.left.prevent="move(-1)"
            x-on:keydown.up.prevent="move(-1)"
        >
            @foreach ($options as $value => $label)
                <button
                    type="button"
                    role="radio"
                    class="cs-swatch"
                    style="--cs: {{ $value }}"
                    x-on:click="state = @js($value)"
                    x-bind:aria-checked="state === @js($value)"
                    x-bind:tabindex="state === @js($value) || (! options.includes(state) && {{ $loop->first ? 'true' : 'false' }}) ? 0 : -1"
                    x-tooltip="{ content: @js($label), theme: $store.theme }"
                    aria-label="{{ $label }}"
                    @disabled($isDisabled())
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                </button>
            @endforeach
        </div>

        <p class="cs-current" aria-live="polite">
            <span class="cs-chip" x-bind:style="'--cs: ' + state"></span>
            <span x-text="labels[state] ?? 'Nenhuma cor escolhida'"></span>
        </p>
    </div>
</x-dynamic-component>

@once
    <style>
        .cs-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .cs-swatch {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            color: transparent;
            background: rgb(var(--cs));
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .12);
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .cs-swatch svg { width: 16px; height: 16px; }
        .cs-swatch:hover { transform: scale(1.08); }
        .cs-swatch[aria-checked="true"] {
            color: #fff;
            box-shadow: 0 0 0 2px rgb(var(--gray-50)), 0 0 0 4px rgb(var(--cs));
        }
        .dark .cs-swatch[aria-checked="true"] { box-shadow: 0 0 0 2px rgb(var(--gray-900)), 0 0 0 4px rgb(var(--cs)); }
        .cs-swatch:focus-visible { outline: 2px solid rgb(var(--primary-500)); outline-offset: 4px; }
        .cs-swatch:disabled { opacity: .5; cursor: not-allowed; }
        .cs-current { display: flex; align-items: center; gap: 8px; margin-top: 12px; font-size: 13px; color: rgb(var(--gray-500)); }
        .dark .cs-current { color: rgb(var(--gray-400)); }
        .cs-chip { width: 12px; height: 12px; border-radius: 4px; background: rgb(var(--cs)); }
        @media (prefers-reduced-motion: reduce) { .cs-swatch { transition: none; } }
    </style>
@endonce
