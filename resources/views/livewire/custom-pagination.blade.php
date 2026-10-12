@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex w-full flex-col items-center justify-between gap-4 sm:flex-row">
        
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-xl bg-muted px-4 py-2 text-sm font-semibold text-muted-foreground opacity-50 cursor-not-allowed">
                    {!! __('Anterior') !!}
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center rounded-xl bg-card border border-border px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted transition-colors shadow-sm">
                    {!! __('Anterior') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center rounded-xl bg-card border border-border px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted transition-colors shadow-sm">
                    {!! __('Próximo') !!}
                </button>
            @else
                <span class="relative inline-flex items-center rounded-xl bg-muted px-4 py-2 text-sm font-semibold text-muted-foreground opacity-50 cursor-not-allowed">
                    {!! __('Próximo') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-muted-foreground">
                    Exibindo de 
                    <span class="font-bold text-foreground">{{ $paginator->firstItem() }}</span>
                    a
                    <span class="font-bold text-foreground">{{ $paginator->lastItem() }}</span>
                    de
                    <span class="font-bold text-foreground">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl overflow-hidden border border-border/50 bg-card p-1">
                    
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('Anterior') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-muted-foreground/40 cursor-not-allowed" aria-hidden="true">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </span>
                        </span>
                    @else
                        <button wire:click="previousPage" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-foreground hover:bg-muted rounded-lg transition-colors" aria-label="{{ __('Anterior') }}">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-muted-foreground">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-primary-foreground bg-primary rounded-lg shadow-sm">{{ $page }}</span>
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground hover:bg-muted hover:text-primary rounded-lg transition-colors" aria-label="Ir para página {{ $page }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" rel="next" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-foreground hover:bg-muted rounded-lg transition-colors" aria-label="{{ __('Próximo') }}">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('Próximo') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-muted-foreground/40 cursor-not-allowed" aria-hidden="true">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                })
            })
        });
    </script>
@endif
