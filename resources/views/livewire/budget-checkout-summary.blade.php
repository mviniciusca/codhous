<div class="space-y-6">
    {{-- Lista de Itens --}}
    <div class="space-y-4">
        @forelse($data['content']['products'] ?? [] as $item)
            @php
                $product = \App\Models\Product::find($item['product']);
                $option = \App\Models\ProductOption::find($item['product_option']);
            @endphp
            @if($product)
                <div class="flex justify-between gap-4 text-sm">
                    <div class="flex-1">
                        <p class="font-bold text-foreground">{{ $product->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $option?->name ?? 'Opção não selecionada' }}</p>
                        <p class="text-[10px] text-muted-foreground italic mt-1">Qtde: {{ $item['quantity'] ?? 0 }} {{ $option?->unit?->value }}</p>
                    </div>
                </div>
            @endif
        @empty
            <div class="py-4 text-center text-sm text-muted-foreground italic border-b border-border/50">
                Seu carrinho está vazio
            </div>
        @endforelse
    </div>

    {{-- Info de Orçamento --}}
    <div class="space-y-4">
        <div class="flex items-start gap-3 rounded-lg border border-primary/20 bg-primary/5 p-4">
            <i data-lucide="info" class="mt-0.5 h-4 w-4 text-primary"></i>
            <p class="text-xs leading-relaxed text-primary/80">
                Os valores e o custo de entrega serão calculados por nossa equipe técnica e enviados no seu WhatsApp em até 24h.
            </p>
        </div>
    </div>

    {{-- Botão de Finalizar --}}
    <div class="pt-2">
        <button type="submit" 
                wire:loading.attr="disabled"
                class="w-full rounded-md bg-primary py-4 px-6 text-sm font-bold uppercase tracking-widest text-primary-foreground transition-all hover:bg-primary/90 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
            <span wire:loading.remove wire:target="create">Solicitar Orçamento Grátis</span>
            <span wire:loading wire:target="create">Enviando...</span>
        </button>
    </div>

    <div class="flex items-center justify-center gap-2 text-[10px] text-muted-foreground uppercase tracking-widest">
        <i data-lucide="shield-check" class="h-3 w-3"></i>
        Ambiente 100% Seguro
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
