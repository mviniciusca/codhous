{{-- Calculadora de volume (modal global e página Ferramentas). A conta é do servidor; aqui só medidas e memória de cálculo --}}
@php
    use App\Services\VolumeCalculator\CalculationResult;
    use App\Services\VolumeCalculator\Expression\Expression;

    $formula = $this->formula;
    $definition = $formula?->definition();
    $calc = $this->calculation;
    $result = $calc['result'];
    $hasExample = $definition && collect($definition->variables)->every(fn ($v) => $v->example !== null);
    $shapeIcons = ['slab' => 'heroicon-m-square-3-stack-3d', 'cylinder' => 'heroicon-m-circle-stack', 'beam' => 'heroicon-m-view-columns', 'custom' => 'heroicon-m-variable'];
    $unitIcons = ['m' => 'heroicon-m-arrows-right-left', 'cm' => 'heroicon-m-arrows-up-down', 'mm' => 'heroicon-m-arrows-up-down', 'm2' => 'heroicon-m-square-2-stack', 'm3' => 'heroicon-m-cube', 'un' => 'heroicon-m-hashtag'];
    $display = $result ? number_format($result->finalVolume, 2, ',', '.') : '0,00';
@endphp

<div @class(['cx-ui vc', 'vc--page' => $variant === 'page', 'vc--modal' => $variant === 'modal'])>
    @include('filament.volume-calculator.styles')

    @if ($this->formulas->isEmpty())
        <div class="cx-empty cx-tone-gray">
            <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-variable" class="cx-icon" /></span>
            <p class="cx-empty-title">Nenhuma fórmula ativa</p>
            <p class="cx-empty-desc">Ative ou crie uma fórmula em Ferramentas › Fórmulas de volume.</p>
        </div>
    @else
    <div class="vc-grid">
        {{-- Medidas --}}
        <section class="vc-card vc-form" aria-label="Medidas">
            <div class="vc-tabs" role="tablist" aria-label="Tipo de peça">
                @foreach ($this->formulas as $item)
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $item->id === $formulaId ? 'true' : 'false' }}"
                        @class(['vc-tab', 'is-active' => $item->id === $formulaId])
                        wire:click="selectFormula({{ $item->id }})"
                        wire:key="vc-tab-{{ $item->id }}"
                    >
                        <x-filament::icon :icon="$shapeIcons[$item->shape] ?? $shapeIcons['custom']" class="cx-icon-sm" />
                        <span>{{ $item->name }}</span>
                    </button>
                @endforeach
            </div>

            @if ($formula?->description)
                <p class="vc-desc">{{ $formula->description }}</p>
            @endif

            <div class="vc-fields">
                @foreach ($definition?->variables ?? [] as $variable)
                    @php $fieldId = 'vc-'.$variant.'-'.$formulaId.'-'.$variable->key; $error = $calc['errors'][$variable->key] ?? null; @endphp
                    <div @class(['vc-field', 'has-error' => $error]) wire:key="vc-field-{{ $formulaId }}-{{ $variable->key }}">
                        <span class="vc-field-icon" aria-hidden="true">
                            <x-filament::icon :icon="$unitIcons[$variable->unit->value] ?? 'heroicon-m-variable'" class="cx-icon-sm" />
                        </span>
                        <div class="vc-field-body">
                            <label for="{{ $fieldId }}" class="vc-label">
                                {{ $variable->label }}
                                <span class="vc-unit">{{ $variable->unit->getLabel() }}</span>
                            </label>
                            <div class="vc-input-wrap">
                                <input
                                    id="{{ $fieldId }}"
                                    type="text"
                                    inputmode="{{ $variable->unit->isInteger() ? 'numeric' : 'decimal' }}"
                                    autocomplete="off"
                                    placeholder="{{ $variable->example !== null ? 'ex.: '.Expression::formatNumber($variable->example) : '0' }}"
                                    class="vc-input"
                                    wire:model.live.debounce.350ms="values.{{ $variable->key }}"
                                    @if ($error) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @elseif ($variable->help) aria-describedby="{{ $fieldId }}-help" @endif
                                />
                                <span class="vc-suffix" aria-hidden="true">{{ $variable->unit->symbol() }}</span>
                            </div>
                            @if ($error)
                                <p class="vc-error" id="{{ $fieldId }}-error">{{ $error }}</p>
                            @elseif ($variable->help)
                                <p class="vc-help" id="{{ $fieldId }}-help">{{ $variable->help }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="vc-margin">
                <span class="vc-field-icon" aria-hidden="true"><x-filament::icon icon="heroicon-m-shield-check" class="cx-icon-sm" /></span>
                <div class="vc-margin-text">
                    <label for="vc-{{ $variant }}-margin" class="vc-label">Margem de segurança</label>
                    <p class="vc-help">Cobre perdas no lançamento, deformação de fôrmas e irregularidades.</p>
                </div>
                <span class="vc-select-wrap">
                    <select id="vc-{{ $variant }}-margin" class="vc-select" wire:model.live="margin">
                        @foreach ($this->marginOptions() as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <x-filament::icon icon="heroicon-m-chevron-down" class="cx-icon-sm" aria-hidden="true" />
                </span>
            </div>

            @if ($calc['error'])
                <p class="vc-alert" role="alert">
                    <x-filament::icon icon="heroicon-m-exclamation-triangle" class="cx-icon-sm" />
                    {{ $calc['error'] }}
                </p>
            @endif

            <div class="vc-save">
                <label for="vc-{{ $variant }}-reference" class="cx-sr">Identificação do cálculo</label>
                <input
                    id="vc-{{ $variant }}-reference"
                    type="text"
                    maxlength="255"
                    class="vc-input"
                    placeholder="Identificação (opcional) — ex.: Obra Rua A, laje do térreo"
                    wire:model="reference"
                    wire:keydown.enter="save"
                />
                <div class="vc-actions">
                    <x-filament::button
                        icon="heroicon-m-bookmark"
                        wire:click="save"
                        wire:target="save"
                        :disabled="! $result"
                    >
                        Salvar no histórico
                    </x-filament::button>
                    <x-filament::button color="gray" icon="heroicon-m-arrow-path" wire:click="clear" wire:target="clear">
                        Limpar
                    </x-filament::button>
                </div>
            </div>
        </section>

        {{-- Resultado e memória de cálculo --}}
        <aside class="vc-side" aria-label="Resultado">
            <div @class(['vc-result', 'is-empty' => ! $result]) aria-live="polite">
                <div class="vc-result-head">
                    <span class="vc-eyebrow">
                        <x-filament::icon icon="heroicon-m-cube" class="cx-icon-sm" />
                        Volume estimado
                    </span>
                    @if ($result)
                        <button
                            type="button"
                            class="vc-copy"
                            x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(@js($display)); copied = true; setTimeout(() => copied = false, 1500)"
                            x-tooltip="{ content: 'Copiar volume', theme: $store.theme }"
                        >
                            <x-filament::icon icon="heroicon-m-clipboard-document" class="cx-icon-sm" x-show="! copied" />
                            <x-filament::icon icon="heroicon-m-check" class="cx-icon-sm" x-show="copied" x-cloak />
                            <span class="cx-sr">Copiar volume</span>
                        </button>
                    @endif
                </div>
                <p class="vc-result-value">
                    <span>{{ $display }}</span><small>m³</small>
                </p>
                <p class="vc-result-note">
                    @if ($result)
                        Com {{ Expression::formatNumber($result->marginPercent, 2) }}% de margem · volume base {{ CalculationResult::formatVolume($result->baseVolume, 3) }}
                    @elseif ($calc['complete'])
                        Corrija as medidas destacadas.
                    @else
                        Preencha as medidas: o volume é calculado enquanto você digita.
                    @endif
                </p>

                @if ($result)
                    <dl class="vc-breakdown">
                        <div><dt>Volume base</dt><dd>{{ CalculationResult::formatVolume($result->baseVolume, 3) }}</dd></div>
                        <div><dt>Margem ({{ Expression::formatNumber($result->marginPercent, 2) }}%)</dt><dd>+ {{ CalculationResult::formatVolume($result->marginVolume, 3) }}</dd></div>
                        @if (abs($result->roundingAdjustment) >= 0.0005)
                            <div><dt>Arredondamento</dt><dd>{{ $result->roundingAdjustment >= 0 ? '+' : '−' }} {{ CalculationResult::formatVolume(abs($result->roundingAdjustment), 3) }}</dd></div>
                        @endif
                        <div class="is-total"><dt>Volume final</dt><dd>{{ CalculationResult::formatVolume($result->finalVolume) }}</dd></div>
                    </dl>
                @endif
            </div>

            <div class="vc-card vc-memory">
                @if ($result)
                    <h3 class="vc-title">
                        <x-filament::icon icon="heroicon-m-document-text" class="cx-icon-sm" />
                        Memória de cálculo
                    </h3>
                    <ol class="vc-steps">
                        <li>
                            <span class="vc-step-label">Fórmula</span>
                            <code class="vc-code">{{ $result->formula }}</code>
                        </li>
                        <li>
                            <span class="vc-step-label">Medidas em metros</span>
                            <ul class="vc-conversions">
                                @foreach ($result->inputs as $input)
                                    <li>
                                        {{ $input['label'] }}:
                                        {{ Expression::formatNumber($input['value']) }} {{ $input['unit'] }}
                                        @if ($input['unit'] !== $input['base_unit'])
                                            = <strong>{{ Expression::formatNumber($input['base_value']) }} {{ $input['base_unit'] }}</strong>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        @foreach ($result->steps as $step)
                            <li>
                                <span class="vc-step-label">{{ $step['label'] }}</span>
                                <code class="vc-code">{{ $step['detail'] }} = {{ Expression::formatNumber($step['value']) }} m³</code>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <x-volume-calculator.shape :shape="$formula?->shape ?? 'custom'" />
                    <h3 class="vc-title">
                        <x-filament::icon icon="heroicon-m-information-circle" class="cx-icon-sm" />
                        Como funciona
                    </h3>
                    <p class="vc-help">
                        Informe as medidas na unidade indicada em cada campo. Elas são convertidas para metros, o volume é calculado pela fórmula
                        <code class="vc-code is-inline">{{ $definition?->expression() ? $definition->expression()->render(fn ($key) => $definition->variablesByKey()[$key]->label ?? $key) : '—' }}</code>
                        e recebe a margem de segurança escolhida.
                    </p>
                    @if ($hasExample)
                        <x-filament::link tag="button" size="sm" icon="heroicon-m-sparkles" wire:click="fillExample">
                            Preencher com um exemplo
                        </x-filament::link>
                    @endif
                @endif

                <p class="vc-footnote">
                    Volume geométrico em m³ de concreto fresco, unidade de medida do concreto dosado em central (ABNT NBR 7212).
                    Não inclui preço, frete nem bombeamento.
                    @if ($formula?->currentVersion)
                        <span class="vc-version">{{ $formula->name }} · {{ $formula->currentVersion->label() }}</span>
                    @endif
                </p>
            </div>
        </aside>
    </div>

    @if ($variant === 'page' && $this->recent->isNotEmpty())
        <section class="vc-card vc-recent" aria-label="Meus últimos cálculos">
            <h3 class="vc-title">
                <x-filament::icon icon="heroicon-m-clock" class="cx-icon-sm" />
                Meus últimos cálculos
            </h3>
            <ul>
                @foreach ($this->recent as $item)
                    <li wire:key="vc-recent-{{ $item->id }}">
                        <span class="vc-recent-main">
                            <strong>{{ $item->reference ?: $item->formulaVersion?->formula?->name }}</strong>
                            <small>{{ $item->formulaVersion?->formula?->name }} {{ $item->formulaVersion?->label() }} · {{ $item->created_at->diffForHumans() }}</small>
                        </span>
                        <span class="vc-recent-volume">{{ CalculationResult::formatVolume($item->final_volume) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
    @endif
</div>
