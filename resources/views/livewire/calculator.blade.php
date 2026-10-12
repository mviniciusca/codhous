@php
    $isPrimaryBg = str_contains($bgColor, 'bg-primary');
    $accentText = $isPrimaryBg ? 'text-primary-950' : 'text-primary-ink';
    $accentBg = $isPrimaryBg ? 'bg-primary-950 text-white' : 'bg-primary text-primary-foreground';
    $accentLightBg = $isPrimaryBg ? 'bg-black/40' : 'bg-primary/10';
    $accentLightBorder = $isPrimaryBg ? 'border-black/50' : 'border-primary/30';
    
    $boxBg = $isPrimaryBg ? 'bg-black/25' : 'bg-white';
    $boxBorder = $isPrimaryBg ? 'border-black/35' : 'border-border/30';
    
    $leftSideTitle = $title ?? 'Calculadora de Volume de Concreto';
    $leftSideSubtitle = $subtitle ?? 'CALCULE COM PRECISÃO';
    $leftSideDesc = $description ?? 'Calcule o volume de concreto usinado do seu projeto de forma rápida e segura, seguindo as regras da norma ABNT NBR 7212 para entrega e transporte.';
    
    $formula = $this->formula;
    $definition = $formula?->definition();
    $calc = $this->calculation;
    $result = $calc['result'];
    $shapeIcons = ['slab' => 'package', 'cylinder' => 'database', 'beam' => 'columns-3', 'custom' => 'square-function'];
    $unitIcons = ['m' => 'arrow-left-right', 'cm' => 'arrow-up-down', 'mm' => 'arrow-up-down', 'm2' => 'square', 'm3' => 'box', 'un' => 'hash'];
    $inputClass = 'w-full rounded-lg border bg-background px-3 py-1.5 pr-10 text-sm font-medium transition-colors focus:outline-none focus:ring-1';

    $isLeft = $headerAlignment === 'left';
    $isRight = $headerAlignment === 'right';
    $isCenter = $headerAlignment === 'center';
    
    $gridClass = 'gap-12 lg:gap-16';
    if ($isCenter) {
        $gridClass .= ' flex flex-col items-center mx-auto max-w-4xl w-full';
    } else {
        $gridClass .= ' grid items-start';
        $gridClass .= $isRight ? ' lg:grid-cols-[450px_1fr]' : ' lg:grid-cols-[1fr_450px]';
    }
@endphp
<div class="{{ $gridClass }}">
    <!-- Textos e Informações -->
    @if($headerVisible)
    <div class="flex flex-col {{ $isCenter ? 'items-center text-center mb-6' : '' }} {{ $isRight ? 'order-2' : 'order-1' }}">
        @if(!empty($leftSideSubtitle))
            <div class="mb-4 inline-flex items-center gap-2 {{ $isCenter ? 'self-center' : 'self-start' }} rounded-full bg-white px-4 py-1.5 shadow-sm border border-border/40 text-scheme-light">
                <div class="flex h-5 w-5 items-center justify-center rounded bg-primary text-primary-foreground">
                    <i data-lucide="calculator" class="h-3 w-3"></i>
                </div>
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-foreground">{{ $leftSideSubtitle }}</span>
            </div>
        @endif

        @if(!empty($leftSideTitle))
            <h2 class="mb-6 text-4xl font-extrabold tracking-tight text-foreground md:text-5xl" style="text-wrap: balance;">
                {!! str_replace('Volume de Concreto', '<span class="text-primary">Volume de Concreto</span>', $leftSideTitle) !!}
            </h2>
        @endif

        @if(!empty($leftSideDesc))
            <p class="mb-8 text-base leading-relaxed text-muted-foreground md:text-lg" style="text-wrap: balance;">
                {{ $leftSideDesc }}
            </p>
        @endif

        <div class="flex flex-col gap-5 {{ $isCenter ? 'items-center text-left max-w-xl mx-auto' : '' }}">
            @foreach ($this->formulas as $item)
                @php $itemDefinition = $item->definition(); @endphp
                <div class="flex items-start gap-4" wire:key="calc-info-{{ $item->id }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-border/30">
                        <i data-lucide="{{ $shapeIcons[$item->shape] ?? $shapeIcons['custom'] }}" class="h-5 w-5 text-primary"></i>
                    </div>
                    <div>
                        <strong class="block text-base font-bold text-foreground">{{ $item->name }}</strong>
                        <p class="text-sm text-muted-foreground">Volume = {{ $itemDefinition->expression()->render(fn ($key) => mb_strtolower($itemDefinition->variablesByKey()[$key]->label ?? $key)) }}</p>
                    </div>
                </div>
            @endforeach

            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-border/30">
                    <i data-lucide="shield-check" class="h-5 w-5 text-primary"></i>
                </div>
                <div>
                    <strong class="block text-base font-bold text-foreground">Margem de segurança</strong>
                    <p class="text-sm leading-relaxed text-muted-foreground">Adicione de 5% a 10% para cobrir perdas operacionais, deformações ou irregularidades.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lado Direito: Calculadora Card -->
    <div class="rounded-[24px] border {{ $boxBorder }} {{ $boxBg }} p-6 shadow-2xl shadow-black/5 text-scheme-light {{ $isCenter ? 'w-full max-w-[450px] mx-auto' : '' }} {{ $isRight ? 'order-1' : 'order-2' }}">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $accentBg }}">
                <i data-lucide="calculator" class="h-5 w-5"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-foreground leading-tight">Calcule seu volume</h3>
                <p class="text-[11px] text-muted-foreground">Preencha as medidas e obtenha o volume.</p>
            </div>
        </div>

        @if ($this->formulas->count() > 1)
            <div class="mb-5 flex flex-wrap gap-1 rounded-2xl bg-muted/50 p-1" role="tablist" aria-label="Tipo de peça">
                @foreach ($this->formulas as $item)
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $item->id === $formulaId ? 'true' : 'false' }}"
                        wire:key="calc-tab-{{ $item->id }}"
                        wire:click="selectFormula({{ $item->id }})"
                        class="flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1.5 text-[13px] font-bold transition-all {{ $item->id === $formulaId ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}"
                    >
                        <i data-lucide="{{ $shapeIcons[$item->shape] ?? $shapeIcons['custom'] }}" class="h-3.5 w-3.5"></i> {{ $item->name }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col gap-4">
            @if (! $definition)
                <p class="rounded-lg bg-muted/50 p-3 text-xs text-muted-foreground">A calculadora está indisponível no momento.</p>
            @endif
            @foreach ($definition?->variables ?? [] as $variable)
                @php
                    $fieldId = 'calc-'.$this->getId().'-'.$variable->key;
                    $error = $calc['errors'][$variable->key] ?? null;
                @endphp
                <div class="flex items-start gap-3" wire:key="calc-field-{{ $formulaId }}-{{ $variable->key }}">
                    <div class="mt-4 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="{{ $unitIcons[$variable->unit->value] ?? 'ruler' }}" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="{{ $fieldId }}" class="text-xs font-bold text-foreground">{{ $variable->label }}</label>
                            <span class="text-[9px] uppercase text-muted-foreground">{{ $variable->unit->getLabel() }}</span>
                        </div>
                        <div class="relative">
                            <input
                                wire:model.live.debounce.350ms="values.{{ $variable->key }}"
                                id="{{ $fieldId }}"
                                type="text"
                                inputmode="{{ $variable->unit->isInteger() ? 'numeric' : 'decimal' }}"
                                autocomplete="off"
                                placeholder="{{ $variable->example !== null ? 'ex.: '.\App\Services\VolumeCalculator\Expression\Expression::formatNumber($variable->example) : '0' }}"
                                @if ($error) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif
                                class="{{ $inputClass }} {{ $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-border/50 focus:border-primary focus:ring-primary' }}"
                            />
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-muted-foreground">{{ $variable->unit->symbol() }}</span>
                        </div>
                        @if ($error)
                            <p id="{{ $fieldId }}-error" class="mt-1 text-[11px] font-medium text-red-600">{{ $error }}</p>
                        @elseif ($variable->help)
                            <p class="mt-1 text-[11px] text-muted-foreground">{{ $variable->help }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 flex items-center justify-between gap-3 rounded-xl bg-muted/50 p-2.5">
            <label for="calc-{{ $this->getId() }}-margin" class="flex items-center gap-2">
                <i data-lucide="shield-check" class="h-4 w-4 text-foreground"></i>
                <span class="text-xs font-bold text-foreground">Margem de segurança</span>
            </label>
            <select id="calc-{{ $this->getId() }}-margin" wire:model.live="margin" class="rounded-lg border border-border/30 bg-white py-1 pl-2 pr-8 text-[11px] font-medium shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                @foreach ($this->marginOptions() as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-5 relative overflow-hidden rounded-[16px] bg-primary-950 p-5 text-white shadow-inner">
            <i data-lucide="package" class="absolute -right-4 -bottom-4 h-24 w-24 text-white/5"></i>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5 mb-1.5 opacity-80">
                    <i data-lucide="package" class="h-3.5 w-3.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-widest">Volume Estimado</span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-mono text-4xl font-extrabold text-primary">{{ number_format($result?->finalVolume ?? 0, 2, ',', '.') }}</span>
                    <span class="text-lg font-medium text-primary">m&sup3;</span>
                </div>
                <p class="mt-1.5 text-[9px] opacity-60">
                    @if($result)
                        Volume sem margem: {{ number_format($result->baseVolume, 2, ',', '.') }} m³ · margem de {{ \App\Services\VolumeCalculator\Expression\Expression::formatNumber($result->marginPercent, 2) }}%
                    @elseif($calc['error'])
                        {{ $calc['error'] }}
                    @else
                        Volume final com margem de segurança.
                    @endif
                </p>
                @if($advice = $this->truckAdvice)
                    <div class="mt-3 rounded-xl border border-amber-400/30 bg-amber-400/10 p-3 text-amber-300">
                        <p class="flex items-center gap-1.5 text-[11px] font-bold leading-tight">
                            <i data-lucide="truck" class="h-3.5 w-3.5"></i>
                            Abaixo do pedido mínimo do caminhão ({{ number_format($advice['min_volume'], 2, ',', '.') }} m³)
                        </p>
                        <p class="mt-1 text-[10px] leading-snug text-amber-100/80">Faltam {{ number_format($advice['missing'], 2, ',', '.') }} m³. Para volumes assim, considere:</p>
                        <ul class="mt-1.5 space-y-1 text-[10px] leading-snug text-amber-100/90">
                            @foreach (array_slice($advice['suggestions'], 0, 3) as $suggestion)
                                <li class="flex items-start gap-1.5">
                                    <i data-lucide="{{ $suggestion['lucide'] }}" class="mt-px h-3 w-3 shrink-0"></i>
                                    <span><strong class="font-bold">{{ $suggestion['title'] }}:</strong> {{ $suggestion['text'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <button type="button" wire:click="$refresh" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-foreground shadow-md shadow-primary/30 transition-all hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0">
            <i data-lucide="calculator" class="h-4 w-4"></i>
            Calcular Volume
        </button>

        <button type="button" wire:click="resetFields" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-transparent px-5 py-2 text-xs font-bold text-muted-foreground transition-all hover:bg-muted/50 hover:text-foreground">
            <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
            Limpar Campos
        </button>
    </div>
</div>

@script
<script>
    // Abas e campos vêm das fórmulas: reaplica os ícones Lucide depois de cada atualização
    Livewire.hook('morphed', ({ component }) => {
        if (component.id === $wire.$id) window.lucide?.createIcons();
    });
</script>
@endscript
