<div class="relative">
    @php
        $isPrimaryBg = str_contains($bgColor ?? '', 'bg-primary');
        $accentText = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
        $accentBg = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)] text-white' : 'bg-primary text-primary-foreground';
        $accentLightBg = $isPrimaryBg ? 'bg-black/20' : 'bg-primary/5';
        $accentLightBorder = $isPrimaryBg ? 'border-black/30' : 'border-primary/20';
    @endphp

    @if($isSubmitted)
        <div class="rounded-3xl border {{ $accentLightBorder }} {{ $accentLightBg }} p-8 text-center md:p-12">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full {{ $accentBg }}">
                <i data-lucide="check" class="h-8 w-8"></i>
            </div>
            <h3 class="font-mono text-2xl font-bold text-foreground">Pedido Recebido com Sucesso!</h3>
            <p class="mt-4 text-muted-foreground">
                Nossa equipe recebeu sua solicitação de orçamento. Em até 24 horas entraremos em contato via WhatsApp ou E-mail para finalizar os detalhes e agendar sua entrega.
            </p>
            <div class="mt-8">
                <button wire:click="resetForm" class="text-sm font-bold uppercase tracking-widest {{ $accentText }} hover:underline">
                    Fazer outro pedido
                </button>
            </div>
        </div>
    @else
        <form wire:submit="create" class="space-y-8">
            {{ $this->form }}
        </form>
    @endif

    @push('scripts')
        @if($turnstileEnabled)
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            <script>
                function onTurnstileSuccess(token) {
                    @this.set('turnstileToken', token);
                }
            </script>
        @endif
    @endpush

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