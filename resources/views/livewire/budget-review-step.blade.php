<div class="space-y-6 py-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Dados Pessoais --}}
        <div class="rounded-xl border border-border bg-muted/30 p-5">
            <div class="mb-3 flex items-center gap-2 text-primary">
                <i data-lucide="user" class="h-4 w-4"></i>
                <h4 class="font-mono text-xs font-bold uppercase tracking-wider">Seus Dados</h4>
            </div>
            <div class="space-y-2 text-sm">
                <p><span class="text-muted-foreground">Nome:</span> <span class="font-bold text-foreground">{{ $data['content']['customer_name'] ?? '-' }}</span></p>
                <p><span class="text-muted-foreground">WhatsApp:</span> <span class="font-bold text-foreground">{{ $data['content']['customer_phone'] ?? '-' }}</span></p>
                <p><span class="text-muted-foreground">E-mail:</span> <span class="font-bold text-foreground">{{ $data['content']['customer_email'] ?? '-' }}</span></p>
            </div>
        </div>

        {{-- Entrega --}}
        <div class="rounded-xl border border-border bg-muted/30 p-5">
            <div class="mb-3 flex items-center gap-2 text-primary">
                <i data-lucide="map-pin" class="h-4 w-4"></i>
                <h4 class="font-mono text-xs font-bold uppercase tracking-wider">Entrega</h4>
            </div>
            <div class="space-y-2 text-sm">
                <p><span class="text-muted-foreground">CEP:</span> <span class="font-bold text-foreground">{{ $data['content']['postcode'] ?? '-' }}</span></p>
                <p><span class="text-muted-foreground">Endereço:</span> 
                    <span class="font-bold text-foreground">
                        {{ $data['content']['street'] ?? '-' }}, {{ $data['content']['number'] ?? '-' }}
                        <br>
                        {{ $data['content']['neighborhood'] ?? '' }} - {{ $data['content']['city'] ?? '' }}/{{ $data['content']['state'] ?? '' }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- Produtos --}}
    <div class="rounded-xl border border-border bg-muted/30 p-5">
        <div class="mb-4 flex items-center gap-2 text-primary">
            <i data-lucide="shopping-bag" class="h-4 w-4"></i>
            <h4 class="font-mono text-xs font-bold uppercase tracking-wider">Produtos Selecionados</h4>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border/50 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">
                        <th class="pb-2">Produto</th>
                        <th class="pb-2">Aplicação</th>
                        <th class="pb-2 text-right">Qtd</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @forelse($data['content']['products'] ?? [] as $item)
                        @php
                            $product = \App\Models\Product::find($item['product']);
                            $option = \App\Models\ProductOption::find($item['product_option']);
                            $location = \App\Models\Location::find($item['location']);
                        @endphp
                        <tr>
                            <td class="py-3">
                                <p class="font-bold text-foreground">{{ $product?->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ $option?->name }}</p>
                            </td>
                            <td class="py-3 text-muted-foreground">{{ $location?->name ?? '-' }}</td>
                            <td class="py-3 text-right font-mono font-bold text-primary">
                                {{ $item['quantity'] }} {{ $option?->unit?->value }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-muted-foreground italic">Nenhum produto adicionado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-start gap-3 rounded-lg border border-primary/20 bg-primary/5 p-4">
        <i data-lucide="info" class="mt-0.5 h-4 w-4 text-primary"></i>
        <p class="text-xs leading-relaxed text-primary/80">
            Confira se todos os dados estão corretos antes de enviar. Nossa equipe entrará em contato via WhatsApp ou E-mail para finalizar seu orçamento e agendar a entrega.
        </p>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
