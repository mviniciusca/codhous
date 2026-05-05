<div class="relative">
    @if($isSubmitted)
        <div class="rounded-3xl border border-primary/20 bg-primary/5 p-8 text-center md:p-12">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-primary-foreground">
                <i data-lucide="check" class="h-8 w-8"></i>
            </div>
            <h3 class="font-mono text-2xl font-bold text-foreground">Pedido Recebido com Sucesso!</h3>
            <p class="mt-4 text-muted-foreground">
                Nossa equipe recebeu sua solicitação de orçamento. Em até 24 horas entraremos em contato via WhatsApp ou E-mail para finalizar os detalhes e agendar sua entrega.
            </p>
            <div class="mt-8">
                <button wire:click="resetForm" class="text-sm font-bold uppercase tracking-widest text-primary hover:underline">
                    Fazer outro pedido
                </button>
            </div>
        </div>
    @else
        <form wire:submit="create" class="space-y-8">
            {{ $this->form }}
        </form>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        document.addEventListener('livewire:navigated', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</div>