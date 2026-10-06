<div id="locacao-equipamentos" class="scroll-mt-24" x-data @scroll-to-equipment.window="document.getElementById('locacao-equipamentos').scrollIntoView({ behavior: 'smooth', block: 'start' })">
    @php
        $companySettings = \App\Models\Setting::get('company', []);
        $rentalSettings = data_get($companySettings, 'equipment_rental', []);
        $isRentalActive = data_get($rentalSettings, 'is_active', true);
        $rentalWhatsApp = data_get($rentalSettings, 'whatsapp_number', '');
        $redirectToContact = data_get($rentalSettings, 'redirect_to_contact', false);
        $contactPageUrl = data_get($rentalSettings, 'contact_page_url') ?: '/atendimento';
    @endphp

    @if(!$isRentalActive)
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border p-12 text-center bg-card/50">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                <i data-lucide="ban" class="h-6 w-6"></i>
            </div>
            <h3 class="mb-1 text-lg font-bold text-foreground">Serviço de Locação Suspenso</h3>
            <p class="text-sm text-muted-foreground">O serviço de locação de equipamentos está temporariamente inativo.</p>
        </div>
    @else
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
                <div x-data="{ showModal: false }" class="group flex h-full flex-col overflow-hidden rounded-[24px] border border-border/40 bg-card shadow-sm transition-all hover:shadow-md hover:border-primary/20">
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
                            <button @click="showModal = true" class="inline-flex h-10 gap-1.5 items-center justify-center rounded-xl bg-muted px-4 text-sm font-semibold text-foreground transition-colors hover:bg-muted/80">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                Detalhes
                            </button>
                            @if($equipment->is_available)
                                @php
                                    $websiteSettings = \App\Models\Setting::get('website', []);
                                    $defaultWhatsapp = data_get($websiteSettings, 'features.whatsapp_widget.number', '');
                                    
                                    if ($redirectToContact) {
                                        // If url is absolute, keep it, otherwise wrap in url()
                                        $actionUrl = str_starts_with($contactPageUrl, 'http') ? $contactPageUrl : url($contactPageUrl);
                                        $btnTarget = "_self";
                                    } else {
                                        $whatsappNumber = !empty($rentalWhatsApp) ? $rentalWhatsApp : $defaultWhatsapp;
                                        $whatsappUrl = $whatsappNumber 
                                            ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode("Olá! Gostaria de saber mais sobre o aluguel do equipamento: {$equipment->name}.")
                                            : '#';
                                        $actionUrl = $whatsappUrl;
                                        $btnTarget = "_blank";
                                    }
                                @endphp
                                <a href="{{ $actionUrl }}" target="{{ $btnTarget }}" class="inline-flex h-10 gap-1.5 items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 hover:shadow-md group">
                                    @if($redirectToContact)
                                        <i data-lucide="calendar" class="h-4 w-4 transition-transform group-hover:scale-110"></i>
                                    @else
                                        <i data-lucide="message-circle" class="h-4 w-4 transition-transform group-hover:scale-110"></i>
                                    @endif
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

                    <!-- Modal de Detalhes -->
                    <template x-teleport="body">
                        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 p-4 backdrop-blur-sm" style="display: none;"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            <div x-show="showModal" @click.away="showModal = false" class="relative w-full max-w-4xl rounded-3xl bg-background p-0 shadow-2xl overflow-hidden"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                            >
                                <button @click="showModal = false" class="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/10 text-foreground hover:bg-black/20 focus:outline-none backdrop-blur-md transition-colors">
                                    <i data-lucide="x" class="h-5 w-5"></i>
                                </button>

                                <div class="flex flex-col md:flex-row">
                                    <div class="w-full md:w-1/2 bg-muted relative">
                                        @if($equipment->image)
                                            <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}" class="w-full h-full object-cover min-h-[300px]">
                                        @else
                                            <div class="flex h-[300px] md:h-full w-full items-center justify-center text-muted-foreground">
                                                <i data-lucide="image" class="h-16 w-16 opacity-20"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="absolute left-4 top-4">
                                            @if($equipment->is_available)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500 text-white px-3 py-1 text-xs font-bold shadow-md">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                                    Disponível
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500 text-white px-3 py-1 text-xs font-bold shadow-md">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                                    Indisponível
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="w-full md:w-1/2 p-8 flex flex-col max-h-[80vh] overflow-y-auto">
                                        @if($equipment->category)
                                            <div class="text-[10px] font-bold tracking-[0.2em] uppercase text-primary mb-2">
                                                {{ $equipment->category }}
                                            </div>
                                        @endif
                                        
                                        <h2 class="text-3xl font-extrabold text-foreground mb-4 leading-tight">{{ $equipment->name }}</h2>

                                        <div class="mb-8">
                                            <h3 class="text-lg font-bold border-b border-border/50 pb-2 mb-4 text-foreground">Descrição</h3>
                                            <div class="prose prose-sm max-w-none text-muted-foreground">
                                                {!! nl2br(e($equipment->description ?? 'Nenhuma descrição detalhada disponível para este equipamento.')) !!}
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-6 border-t border-border/50 grid grid-cols-1 gap-3">
                                            @if($equipment->is_available)
                                                @php
                                                    $websiteSettings = \App\Models\Setting::get('website', []);
                                                    $defaultWhatsapp = data_get($websiteSettings, 'features.whatsapp_widget.number', '');
                                                    
                                                    if ($redirectToContact) {
                                                        $actionUrl = str_starts_with($contactPageUrl, 'http') ? $contactPageUrl : url($contactPageUrl);
                                                        $btnTarget = "_self";
                                                    } else {
                                                        $whatsappNumber = !empty($rentalWhatsApp) ? $rentalWhatsApp : $defaultWhatsapp;
                                                        $whatsappUrl = $whatsappNumber 
                                                            ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode("Olá! Gostaria de fazer um orçamento para alugar o equipamento: {$equipment->name}.")
                                                            : '#';
                                                        $actionUrl = $whatsappUrl;
                                                        $btnTarget = "_blank";
                                                    }
                                                @endphp
                                                <a href="{{ $actionUrl }}" target="{{ $btnTarget }}" class="group flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 text-sm font-bold text-primary-foreground shadow-sm transition-all hover:bg-primary/90 hover:shadow-md">
                                                    @if($redirectToContact)
                                                        <i data-lucide="mail" class="h-5 w-5"></i>
                                                    @else
                                                        <i data-lucide="calendar" class="h-5 w-5"></i>
                                                    @endif
                                                    Reservar Agora
                                                    <i data-lucide="arrow-right" class="h-4 w-4 ml-2 transition-transform group-hover:translate-x-1"></i>
                                                </a>
                                            @else
                                                <button disabled class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-muted px-6 text-sm font-bold text-muted-foreground opacity-50 cursor-not-allowed">
                                                    <i data-lucide="ban" class="h-5 w-5"></i>
                                                    Equipamento Indisponível
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-8 flex justify-center w-full">
            {{ $equipments->links('livewire.custom-pagination') }}
        </div>
    @endif
    @endif
</div>
