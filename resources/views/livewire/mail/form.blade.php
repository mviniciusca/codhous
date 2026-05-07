<div>
    @if($sent)
        <div class="text-center py-10 space-y-4">
            <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-foreground">Mensagem Enviada!</h2>
            <p class="text-muted-foreground">Obrigado pelo seu contato. Nossa equipe analisará sua mensagem e retornará o mais breve possível.</p>
            <button wire:click="$set('sent', false)" class="mt-6 text-sm font-semibold text-primary hover:underline">
                Enviar outra mensagem
            </button>
        </div>
    @else
        <form wire:submit="create" class="space-y-5">
            {{ $this->form }}

            <button type="submit" class="w-full rounded-lg bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                <span wire:loading.remove>Enviar Mensagem</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>
    @endif

    <x-filament-actions::modals />
</div>
