<div>
    <!-- Barra de Filtros -->
    <div class="mb-8 flex flex-col gap-4 rounded-2xl bg-card border border-border/50 p-4 shadow-sm md:flex-row md:items-center md:justify-between">
        
        <!-- Busca -->
        <div class="relative w-full md:max-w-xs">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted-foreground">
                <i data-lucide="search" class="h-4 w-4"></i>
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Buscar equipamentos..." 
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-10 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
        </div>

        <!-- Filtros Direita -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <!-- Categoria -->
            <select wire:model.live="category" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 min-w-[150px]">
                <option value="">Todas Categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <!-- Disponibilidade -->
            <select wire:model.live="availability" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 min-w-[150px]">
                <option value="all">Status: Todos</option>
                <option value="available">Disponível</option>
                <option value="unavailable">Indisponível</option>
            </select>

            <!-- Ordenação -->
            <select wire:model.live="sort" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 min-w-[150px]">
                <option value="recent">Mais recentes</option>
                <option value="name_asc">Nome (A-Z)</option>
                <option value="name_desc">Nome (Z-A)</option>
            </select>
        </div>
    </div>

    <!-- Grid de Equipamentos -->
    @if($equipments->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border p-12 text-center bg-card/50">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                <i data-lucide="package-search" class="h-6 w-6"></i>
            </div>
            <h3 class="mb-1 text-lg font-bold text-foreground">Nenhum equipamento encontrado</h3>
            <p class="text-sm text-muted-foreground">Tente alterar os filtros ou sua busca para encontrar o que procura.</p>
            <button wire:click="$set('search', '')" class="mt-6 text-sm font-semibold text-primary hover:underline">
                Limpar filtros
            </button>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($equipments as $equipment)
                <div class="group flex h-full flex-col overflow-hidden rounded-[24px] border border-border/40 bg-card shadow-sm transition-all hover:shadow-md hover:border-primary/20">
                    <!-- Imagem -->
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-muted">
                        @if($equipment->image)
                            <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-muted text-muted-foreground">
                                <i data-lucide="image" class="h-10 w-10 opacity-20"></i>
                            </div>
                        @endif
                        
                        <!-- Tags -->
                        <div class="absolute left-3 top-3 flex flex-col gap-2">
                            @if($equipment->category)
                                <span class="inline-flex items-center rounded-full bg-background/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-foreground shadow-sm backdrop-blur-md">
                                    {{ $equipment->category }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute right-3 top-3">
                            @if($equipment->is_available)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 text-xs font-semibold text-emerald-600 backdrop-blur-md">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Disponível
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 px-2.5 py-1 text-xs font-semibold text-rose-600 backdrop-blur-md">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    Indisponível
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="mb-2 text-lg font-bold text-foreground line-clamp-1" title="{{ $equipment->name }}">
                            {{ $equipment->name }}
                        </h3>
                        
                        <p class="mb-6 text-sm text-muted-foreground line-clamp-2">
                            {{ $equipment->description ?? 'Nenhuma descrição disponível.' }}
                        </p>

                        <!-- Botões -->
                        <div class="mt-auto grid grid-cols-2 gap-3">
                            <button class="inline-flex h-10 gap-1.5 items-center justify-center rounded-xl bg-muted px-4 text-sm font-semibold text-foreground transition-colors hover:bg-muted/80">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                Detalhes
                            </button>
                            @if($equipment->is_available)
                                @php
                                    $whatsappNumber = data_get(\App\Models\Setting::get('website', []), 'features.whatsapp_widget.number', '');
                                    $whatsappUrl = $whatsappNumber 
                                        ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode("Olá! Gostaria de saber mais sobre o aluguel do equipamento: {$equipment->name}.")
                                        : '#';
                                @endphp
                                <a href="{{ $whatsappUrl }}" target="_blank" class="inline-flex h-10 gap-1.5 items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 hover:shadow-md group">
                                    <i data-lucide="message-circle" class="h-4 w-4 transition-transform group-hover:scale-110"></i>
                                    Alugar
                                </a>
                            @else
                                <button disabled class="inline-flex h-10 gap-1.5 items-center justify-center rounded-xl bg-muted px-4 text-sm font-semibold text-muted-foreground opacity-50 cursor-not-allowed">
                                    <i data-lucide="ban" class="h-4 w-4"></i>
                                    Indisponível
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-8 flex justify-center w-full">
            {{ $equipments->links('livewire.custom-pagination') }}
        </div>
    @endif
</div>
