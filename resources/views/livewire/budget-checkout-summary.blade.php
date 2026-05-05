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

    {{-- Totais --}}
    <div class="space-y-2 border-t border-border pt-4">
        <div class="flex justify-between text-sm">
            <span class="text-muted-foreground">Subtotal</span>
            <span class="font-mono font-bold">R$ {{ number_format($data['content']['subtotal'] ?? 0, 2, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-muted-foreground">Frete</span>
            @if(($data['content']['shipping'] ?? 0) > 0)
                <span class="font-mono font-bold text-primary">R$ {{ number_format($data['content']['shipping'], 2, ',', '.') }}</span>
            @else
                <span class="text-[10px] font-bold uppercase text-muted-foreground">A calcular</span>
            @endif
        </div>
        <div class="flex justify-between border-t border-border pt-4 text-lg font-bold">
            <span>Total Estimado</span>
            <span class="font-mono text-primary">R$ {{ number_format($data['content']['total'] ?? 0, 2, ',', '.') }}</span>
        </div>
    </div>

    {{-- Botão de Finalizar --}}
    <div class="pt-4">
        <button type="submit" 
                wire:loading.attr="disabled"
                class="w-full rounded-md bg-primary py-4 px-6 text-sm font-bold uppercase tracking-widest text-primary-foreground shadow-lg transition-all hover:bg-primary/90 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
            <span wire:loading.remove>Finalizar Orçamento</span>
            <span wire:loading>Processando...</span>
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
