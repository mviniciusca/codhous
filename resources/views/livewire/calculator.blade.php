@php
    $isPrimaryBg = str_contains($bgColor, 'bg-primary');
    $accentText = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
    $accentBg = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)] text-white' : 'bg-primary text-primary-foreground';
    $accentLightBg = $isPrimaryBg ? 'bg-black/40' : 'bg-primary/10';
    $accentLightBorder = $isPrimaryBg ? 'border-black/50' : 'border-primary/30';
    
    $boxBg = $isPrimaryBg ? 'bg-black/25' : 'bg-white';
    $boxBorder = $isPrimaryBg ? 'border-black/35' : 'border-border/30';
    
    $leftSideTitle = $title ?? 'Calculadora de Volume de Concreto';
    $leftSideSubtitle = $subtitle ?? 'CALCULE COM PRECISÃO';
    $leftSideDesc = $description ?? 'Calcule o volume de concreto usinado do seu projeto de forma rápida e segura, seguindo as regras da norma ABNT NBR 7212 para entrega e transporte.';
    
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
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-border/30">
                    <i data-lucide="package" class="h-5 w-5 text-primary"></i>
                </div>
                <div>
                    <strong class="block text-base font-bold text-foreground">Lajes e pisos</strong>
                    <p class="text-sm text-muted-foreground">Volume = Comprimento × Largura × Espessura</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-border/30">
                    <i data-lucide="database" class="h-5 w-5 text-primary"></i>
                </div>
                <div>
                    <strong class="block text-base font-bold text-foreground">Pilares e estacas (cilíndricos)</strong>
                    <p class="text-sm text-muted-foreground">Volume = π × Raio² × Altura</p>
                </div>
            </div>

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

        <div class="mb-5 flex rounded-full bg-muted/50 p-1">
            <button wire:click="$set('shape', 'retangular')" class="flex flex-1 items-center justify-center gap-1.5 rounded-full px-3 py-1.5 text-[13px] font-bold transition-all {{ $shape === 'retangular' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}">
                <i data-lucide="package" class="h-3.5 w-3.5"></i> Lajes / Pisos
            </button>
            <button wire:click="$set('shape', 'cilindrico')" class="flex flex-1 items-center justify-center gap-1.5 rounded-full px-3 py-1.5 text-[13px] font-bold transition-all {{ $shape === 'cilindrico' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}">
                <i data-lucide="database" class="h-3.5 w-3.5"></i> Pilares / Estacas
            </button>
        </div>

        <div class="flex flex-col gap-4">
            @if($shape === 'retangular')
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="ruler" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="calc-largura" class="text-xs font-bold text-foreground">Largura</label>
                            <span class="text-[9px] uppercase text-muted-foreground">metros (m)</span>
                        </div>
                        <input wire:model.live="width" id="calc-largura" type="number" step="0.01" min="0" placeholder="0,00" class="w-full rounded-lg border border-border/50 bg-background px-3 py-1.5 text-sm font-medium transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="arrow-left-right" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="calc-comprimento" class="text-xs font-bold text-foreground">Comprimento</label>
                            <span class="text-[9px] uppercase text-muted-foreground">metros (m)</span>
                        </div>
                        <input wire:model.live="length" id="calc-comprimento" type="number" step="0.01" min="0" placeholder="0,00" class="w-full rounded-lg border border-border/50 bg-background px-3 py-1.5 text-sm font-medium transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="arrow-up-down" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="calc-espessura" class="text-xs font-bold text-foreground">Espessura</label>
                            <span class="text-[9px] uppercase text-muted-foreground">centímetros (cm)</span>
                        </div>
                        <input wire:model.live="thickness_cm" id="calc-espessura" type="number" step="1" min="0" placeholder="0" class="w-full rounded-lg border border-border/50 bg-background px-3 py-1.5 text-sm font-medium transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="circle" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="calc-raio" class="text-xs font-bold text-foreground">Raio</label>
                            <span class="text-[9px] uppercase text-muted-foreground">centímetros (cm)</span>
                        </div>
                        <input wire:model.live="radius_cm" id="calc-raio" type="number" step="1" min="0" placeholder="0" class="w-full rounded-lg border border-border/50 bg-background px-3 py-1.5 text-sm font-medium transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted/50">
                        <i data-lucide="arrow-up-down" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-0.5">
                            <label for="calc-altura" class="text-xs font-bold text-foreground">Altura/Prof</label>
                            <span class="text-[9px] uppercase text-muted-foreground">metros (m)</span>
                        </div>
                        <input wire:model.live="height" id="calc-altura" type="number" step="0.01" min="0" placeholder="0,00" class="w-full rounded-lg border border-border/50 bg-background px-3 py-1.5 text-sm font-medium transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-5 flex items-center justify-between rounded-xl bg-muted/50 p-2.5">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="h-4 w-4 text-foreground"></i>
                <span class="text-xs font-bold text-foreground">Margem de segurança</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-lg bg-white px-2 py-1 text-[11px] font-medium shadow-sm border border-border/30">
                10% (recomendado) <i data-lucide="chevron-down" class="h-3 w-3"></i>
            </div>
        </div>

        <div class="mt-5 relative overflow-hidden rounded-[16px] bg-slate-900 p-5 text-white shadow-inner">
            <i data-lucide="package" class="absolute -right-4 -bottom-4 h-24 w-24 text-white/5"></i>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5 mb-1.5 opacity-80">
                    <i data-lucide="package" class="h-3.5 w-3.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-widest">Volume Estimado</span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-mono text-4xl font-extrabold text-primary">{{ number_format($volumeWithMargin > 0 ? $volumeWithMargin : $volume, 2) }}</span>
                    <span class="text-lg font-medium text-primary">m&sup3;</span>
                </div>
                <p class="mt-1.5 text-[9px] opacity-60">
                    @if($volume > 0)
                        Volume sem margem: {{ number_format($volume, 2) }} m³
                    @else
                        Volume final com margem de segurança.
                    @endif
                </p>
                @if($volumeWithMargin > 0 && $volumeWithMargin < 3)
                    <p class="mt-2 text-[9px] text-amber-400 font-medium leading-tight">
                        ⚠️ Atenção: Para volumes < 3m³, considere usar betoneira.
                    </p>
                @endif
            </div>
        </div>

        <button wire:click="calculate" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-foreground shadow-md shadow-primary/30 transition-all hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0">
            <i data-lucide="calculator" class="h-4 w-4"></i>
            Calcular Volume
        </button>

        <button wire:click="resetFields" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-transparent px-5 py-2 text-xs font-bold text-muted-foreground transition-all hover:bg-muted/50 hover:text-foreground">
            <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
            Limpar Campos
        </button>
    </div>
</div>
