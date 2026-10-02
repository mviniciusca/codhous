@php
    $isPrimaryBg = str_contains($bgColor, 'bg-primary');
    $accentText = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
    $accentBg = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)] text-white' : 'bg-primary text-primary-foreground';
    $accentLightBg = $isPrimaryBg ? 'bg-black/20' : 'bg-primary/10';
    $accentLightBorder = $isPrimaryBg ? 'border-black/30' : 'border-primary/30';
@endphp
<div class="grid items-start gap-12 lg:grid-cols-2 lg:gap-20">
    <div>
        <p class="text-sm leading-relaxed text-muted-foreground text-justify">
            O volume de concreto usinado é calculado multiplicando as dimensões da peça estrutural, devendo seguir as regras da norma <strong>ABNT NBR 7212</strong> para entrega e transporte.
        </p>

        <div class="mt-8 flex flex-col gap-5 text-sm text-muted-foreground">
            <div>
                <strong class="text-foreground">Como calcular o volume (m³)</strong>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li><strong>Lajes e pisos:</strong> Volume = Comprimento × Largura × Espessura</li>
                    <li><strong>Pilares e estacas (cilíndricos):</strong> Volume = π × Raio² × Altura</li>
                </ul>
            </div>

            <div>
                <strong class="text-foreground">Margem de segurança</strong>
                <p class="mt-1">Adicione cerca de 5% a 10% a mais no pedido para cobrir perdas operacionais, deformação de formas ou irregularidades no terreno.</p>
            </div>

            <div>
                <strong class="text-foreground">Regras da ABNT NBR 7212</strong>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li><strong>Volume mínimo:</strong> A entrega por caminhão-betoneira deve ser de no mínimo 3 m³.</li>
                    <li><strong>Múltiplos:</strong> Os pedidos devem ser feitos em volumes múltiplos de 0,5 m³.</li>
                    <li>A verificação do volume entregue pode ser feita dividindo a massa total do lote pela massa específica do concreto fresco.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-black/10 bg-black/5 [.text-scheme-dark_&]:border-white/10 [.text-scheme-dark_&]:bg-white/5 p-8">
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div wire:ignore class="flex h-10 w-10 items-center justify-center rounded-lg {{ $accentLightBg }}">
                    <i data-lucide="calculator" class="h-5 w-5 {{ $accentText }}"></i>
                </div>
                <h3 class="font-mono text-lg font-bold text-foreground">Calcule Agora</h3>
            </div>

            <div class="flex rounded-md border border-black/20 bg-black/5 [.text-scheme-dark_&]:border-white/20 [.text-scheme-dark_&]:bg-white/5 p-1 w-full sm:w-auto">
                <button wire:click="$set('shape', 'retangular')" class="flex-1 rounded px-3 py-1.5 text-xs font-semibold transition-colors {{ $shape === 'retangular' ? $accentBg . ' [.text-scheme-dark_&]:bg-white [.text-scheme-dark_&]:text-primary' : 'text-foreground/60 [.text-scheme-dark_&]:text-white/60 hover:text-foreground [.text-scheme-dark_&]:hover:text-white' }}">Lajes/Pisos</button>
                <button wire:click="$set('shape', 'cilindrico')" class="flex-1 rounded px-3 py-1.5 text-xs font-semibold transition-colors {{ $shape === 'cilindrico' ? $accentBg . ' [.text-scheme-dark_&]:bg-white [.text-scheme-dark_&]:text-primary' : 'text-foreground/60 [.text-scheme-dark_&]:text-white/60 hover:text-foreground [.text-scheme-dark_&]:hover:text-white' }}">Cilindros</button>
            </div>
        </div>

        <div class="flex flex-col gap-5">
            @if($shape === 'retangular')
                <div>
                    <x-ui.label for="calc-largura" class="text-foreground/60 [.text-scheme-dark_&]:text-white/60">Largura (metros)</x-ui.label>
                    <x-ui.input wire:model.live="width" id="calc-largura" type="number" step="0.01" min="0" placeholder="Ex: 5.00" class="!bg-background" />
                </div>
                <div>
                    <x-ui.label for="calc-comprimento" class="text-foreground/60 [.text-scheme-dark_&]:text-white/60">Comprimento (metros)</x-ui.label>
                    <x-ui.input wire:model.live="length" id="calc-comprimento" type="number" step="0.01" min="0" placeholder="Ex: 10.00" class="!bg-background" />
                </div>
                <div>
                    <x-ui.label for="calc-espessura" class="text-foreground/60 [.text-scheme-dark_&]:text-white/60">Espessura (Centímetros)</x-ui.label>
                    <x-ui.input wire:model.live="thickness_cm" id="calc-espessura" type="number" step="1" min="0" placeholder="Ex: 10" class="!bg-background" />
                </div>
            @else
                <div>
                    <x-ui.label for="calc-raio" class="text-foreground/60 [.text-scheme-dark_&]:text-white/60">Raio (Centímetros)</x-ui.label>
                    <x-ui.input wire:model.live="radius_cm" id="calc-raio" type="number" step="1" min="0" placeholder="Ex: 30" class="!bg-background" />
                    <p class="mt-1 text-[10px] text-foreground/40 [.text-scheme-dark_&]:text-white/40">Raio = Metade do Diâmetro</p>
                </div>
                <div>
                    <x-ui.label for="calc-altura" class="text-foreground/60 [.text-scheme-dark_&]:text-white/60">Altura/Profundidade (metros)</x-ui.label>
                    <x-ui.input wire:model.live="height" id="calc-altura" type="number" step="0.01" min="0" placeholder="Ex: 3.50" class="!bg-background" />
                </div>
            @endif
        </div>

        <div class="mt-8 rounded-lg border {{ $accentLightBorder }} {{ $accentLightBg }} p-6">
            <p class="text-xs font-semibold uppercase tracking-wider {{ $accentText }} opacity-80">Volume Total</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-4xl font-bold {{ $accentText }}">{{ number_format($volume, 2) }}</span>
                <span class="text-lg font-medium {{ $accentText }} opacity-70">m&sup3;</span>
            </div>
            @if($volume > 0)
                <p class="mt-2 text-xs text-foreground/60 [.text-scheme-dark_&]:text-white/60 font-medium">
                    Recomendado (com 10% de margem): <span class="font-bold text-foreground">{{ number_format($volumeWithMargin, 2) }} m³</span>
                </p>

                @if($volumeWithMargin < 3)
                    <div class="mt-4 rounded border border-amber-500/30 bg-amber-500/10 p-3 text-xs text-amber-600 dark:text-amber-200">
                        <strong class="block mb-1 text-amber-600 dark:text-amber-500">⚠️ Atenção à ABNT NBR 7212</strong>
                        O volume recomendado ({{ number_format($volumeWithMargin, 2) }} m³) é <strong>menor que 3m³</strong>. Para volumes pequenos, é aconselhável produzir o concreto localmente na obra usando betoneira própria, pois a maioria das concreteiras exige um pedido mínimo de 3m³ para entrega ou cobra taxas de "frete morto" (taxa de ociosidade).
                    </div>
                @endif
            @endif
        </div>

        <button wire:click="resetFields" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md border border-black/10 [.text-scheme-dark_&]:border-white/10 px-4 py-3 text-sm font-medium text-foreground/60 [.text-scheme-dark_&]:text-white/60 transition-colors hover:bg-black/5 hover:text-foreground [.text-scheme-dark_&]:hover:bg-white/10 [.text-scheme-dark_&]:hover:text-white">
            <span wire:ignore class="flex items-center justify-center"><i data-lucide="rotate-ccw" class="h-4 w-4"></i></span>
            Limpar Campos
        </button>
    </div>
</div>
