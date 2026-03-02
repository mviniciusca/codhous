<div>
    @if(!$isSubmitted)
        <form wire:submit='create' class="flex flex-col gap-6">
         
            {{ $this->form }}

            <button type="submit" class="group mt-2 inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 text-base font-bold uppercase tracking-wide text-primary-foreground transition-all hover:bg-primary/90 focus:outline-none">
                Enviar Solicitação
                <span wire:ignore class="flex items-center justify-center">
                    <i data-lucide="send" class="h-5 w-5 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                </span>
            </button>
        </form>
    @else
        <div class="flex flex-col items-center justify-center p-8 rounded-xl bg-background/5 border border-background/10 space-y-6 animate-in fade-in zoom-in duration-700">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary">
                <span wire:ignore>
                    <!-- inline check SVG (Lucide style) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </div>
            
            <div class="text-center space-y-3">
                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground">
                    Muito <span class="text-primary">Obrigado!</span>
                </h2>
                <p class="text-lg leading-relaxed text-foreground/70">
                    Sua solicitação de orçamento foi recebida com sucesso.
                </p>
                
                <div class="inline-block rounded-full border border-primary/30 bg-primary/10 px-4 py-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-primary">Retorno em até 24 horas</p>
                </div>
            </div>

            <div class="pt-4 w-full">
                <button wire:click="resetForm" class="flex w-full items-center justify-center gap-2 rounded-md border border-primary/20 bg-primary/10 px-6 py-4 text-xs font-semibold uppercase tracking-widest text-primary transition-colors hover:bg-primary/20 hover:text-primary-foreground focus:outline-none">
                    <span wire:ignore><i data-lucide="rotate-ccw" class="h-4 w-4 text-primary"></i></span>
                    Nova Solicitação
                </button>
            </div>
        </div>
    @endif  
</div>