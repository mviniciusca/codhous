<section id="calculadora" class="bg-foreground py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div>
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Ferramenta Interativa</span>
                <h2 class="font-mono text-3xl font-bold tracking-tight text-background md:text-4xl" style="text-wrap: balance;">Calculadora de Volume de Concreto</h2>
                <p class="mt-4 text-lg leading-relaxed text-background/60">Calcule rapidamente o volume de concreto necessário para sua obra. Insira as dimensões e obtenha o resultado em metros cúbicos instantaneamente.</p>
                <div class="mt-8 flex flex-col gap-3 text-sm text-background/40">
                    <p>Fórmula: Volume = Largura &times; Comprimento &times; Espessura</p>
                    <p>Recomendamos adicionar 10% de margem de segurança ao resultado.</p>
                </div>
            </div>

            <div  class="rounded-lg border border-background/10 bg-background/5 p-8">
                <div class="mb-8 flex items-center gap-3">
                    <div wire:ignore class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <i data-lucide="calculator" class="h-5 w-5 text-primary"></i>
                    </div>
                    <h3 class="font-mono text-lg font-bold text-background">Calcule Agora</h3>
                </div>

                <div class="flex flex-col gap-5">
                    <div>
                        <label for="calc-largura" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">Largura (metros)</label>
                        <input wire:model.live="width" id="calc-largura" type="number" step="0.01" min="0" placeholder="Ex: 5.00"
                            class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background placeholder:text-background/30 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label for="calc-comprimento" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">Comprimento (metros)</label>
                        <input wire:model.live="length" id="calc-comprimento" type="number" step="0.01" min="0" placeholder="Ex: 10.00"
                            class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background placeholder:text-background/30 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
    <label for="calc-espessura" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">
        Espessura (Centímetros)
    </label>
    <input wire:model.live="thickness_cm" id="calc-espessura" type="number" step="1" min="0" placeholder="Ex: 10"
        class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background ...">
    <p class="mt-1 text-[10px] text-background/30 italic">O sistema converte automaticamente para metros.</p>
</div>
                </div>

                <div class="mt-8 rounded-lg border border-primary/30 bg-primary/10 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-primary/80">Volume Total</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-mono text-4xl font-bold text-primary">{{ number_format($volume, 2) }}</span>
                        <span class="text-lg font-medium text-primary/70">m&sup3;</span>
                    </div>
                    @if($volume > 0)
                    <p class="mt-2 text-xs text-background/40">
                        Com margem de 10%: <span class="font-semibold text-background/60">{{ number_format($volumeWithMargin, 2) }} m³</span>
                    </p>
                    @endif
                </div>

                <button wire:click="resetFields" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md border border-background/10 px-4 py-3 text-sm font-medium text-background/60 transition-colors hover:bg-background/5 hover:text-background">
                    <span wire:ignore class="flex items-center justify-center"><i data-lucide="rotate-ccw" class="h-4 w-4"></i></span>
                    Limpar Campos
                </button>
            </div>
        </div>
    </div>
</section>
