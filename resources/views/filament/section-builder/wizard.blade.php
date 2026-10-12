{{--
    Inspetor do construtor de seções: o mesmo comportamento do Wizard do Filament
    (validação por etapa via wizard::nextStep), com etapas compactas, corpo rolável
    e rodapé fixo com cancelar / voltar / continuar / criar.
--}}
@php
    $statePath = $getStatePath();
    $steps = collect($getChildComponentContainer()->getComponents())
        ->filter(static fn (\Filament\Forms\Components\Wizard\Step $step): bool => $step->isVisible())
        ->values();
    // Na edição o registro já é válido: salvar fica disponível em qualquer etapa
    $canSubmitAnywhere = $getContainer()->getOperation() === 'edit';
@endphp

<div
    wire:ignore.self
    x-cloak
    x-data="{
        step: null,
        labels: @js($steps->mapWithKeys(fn ($step) => [$step->getId() => $step->getLabel()])),

        nextStep: function () {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= this.getSteps().length) {
                return
            }

            this.step = this.getSteps()[nextStepIndex]
            this.scroll()
        },

        previousStep: function () {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getSteps()[previousStepIndex]
            this.scroll()
        },

        scroll: function () {
            this.$nextTick(() => this.$refs.body?.scrollTo({ top: 0, behavior: 'smooth' }))
        },

        getStepIndex: function (step) {
            let index = this.getSteps().findIndex((indexedStep) => indexedStep === step)

            return index === -1 ? 0 : index
        },

        getSteps: function () {
            return JSON.parse(this.$refs.stepsData.value)
        },

        isFirstStep: function () {
            return this.getStepIndex(this.step) <= 0
        },

        isLastStep: function () {
            return this.getStepIndex(this.step) + 1 >= this.getSteps().length
        },

        isStepAccessible: function (stepId) {
            return @js($isSkippable()) || this.getStepIndex(this.step) > this.getStepIndex(stepId)
        },

        nextLabel: function () {
            const next = this.getSteps()[this.getStepIndex(this.step) + 1]

            return next ? 'Continuar para ' + this.labels[next] : 'Continuar'
        },
    }"
    x-init="step = getSteps().at({{ $getStartStep() - 1 }})"
    x-on:next-wizard-step.window="if ($event.detail.statePath === '{{ $statePath }}') nextStep()"
    {{
        $attributes
            ->merge(['id' => $getId()], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['fi-fo-wizard sb-inspector'])
    }}
>
    <input type="hidden" value="{{ $steps->map(fn ($step) => $step->getId())->toJson() }}" x-ref="stepsData" />

    <ol class="sb-steps" role="list" aria-label="Etapas">
        @foreach ($steps as $step)
            <li
                class="sb-step"
                x-bind:class="{
                    'is-active': getStepIndex(step) === {{ $loop->index }},
                    'is-done': getStepIndex(step) > {{ $loop->index }},
                }"
            >
                <button
                    type="button"
                    class="sb-step-btn"
                    x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                    x-bind:disabled="! isStepAccessible(@js($step->getId()))"
                    x-on:click="step = @js($step->getId())"
                    title="{{ $step->getDescription() }}"
                >
                    <span class="sb-step-num">
                        <span x-show="getStepIndex(step) <= {{ $loop->index }}">{{ $loop->iteration }}</span>
                        <svg x-show="getStepIndex(step) > {{ $loop->index }}" x-cloak viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                    </span>
                    <span class="sb-step-text">
                        <span class="sb-step-label">{{ $step->getLabel() }}</span>
                        <span class="sb-step-desc">{{ $step->getDescription() }}</span>
                    </span>
                </button>
            </li>
        @endforeach
    </ol>

    <div class="sb-inspector-body" x-ref="body">
        @foreach ($steps as $step)
            {{ $step }}
        @endforeach
    </div>

    <div class="sb-inspector-foot">
        <div class="sb-foot-start">
            <span x-show="isFirstStep()">{{ $getCancelAction() }}</span>
            <span x-show="! isFirstStep()" x-cloak>
                <x-filament::button color="gray" icon="heroicon-m-arrow-left" x-on:click="previousStep">
                    Voltar
                </x-filament::button>
            </span>
        </div>

        <div class="sb-foot-end">
            <span
                x-cloak
                data-wizard-next
                x-show="! isLastStep()"
                x-on:click="$wire.dispatchFormEvent('wizard::nextStep', '{{ $statePath }}', getStepIndex(step))"
                wire:loading.class="pointer-events-none opacity-70"
            >
                @if ($canSubmitAnywhere)
                    {{-- Edição: navegar entre etapas é secundário; salvar é a ação principal --}}
                    <x-filament::button color="gray" icon="heroicon-m-arrow-right" icon-position="after">
                        Próxima
                    </x-filament::button>
                @else
                    <x-filament::button icon="heroicon-m-arrow-right" icon-position="after">
                        <span x-text="nextLabel()">Continuar</span>
                    </x-filament::button>
                @endif
            </span>

            @if ($canSubmitAnywhere)
                <span class="sb-foot-submit">{{ $getSubmitAction() }}</span>
            @else
                <span x-show="isLastStep()" x-cloak class="sb-foot-submit">{{ $getSubmitAction() }}</span>
            @endif
        </div>
    </div>
</div>
