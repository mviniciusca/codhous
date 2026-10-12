<div class="grid w-full items-center gap-4 md:grid-cols-[1fr_1.7fr] md:gap-8">
    <div class="flex items-center gap-3">
        <span class="icon-box hidden sm:inline-flex"><i data-lucide="mail-plus"></i></span>
        <div>
            <h4 class="font-mono text-base font-bold text-foreground">Assine nossa newsletter</h4>
            <p class="text-xs text-muted-foreground">Novidades e condições especiais, sem spam.</p>
        </div>
    </div>
    @if($sent)
        <div class="rounded-control border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-700 dark:text-emerald-300">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="h-5 w-5"></i>
                <p class="text-sm font-medium">Inscrição realizada com sucesso! Fique de olho na sua caixa de entrada.</p>
            </div>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col gap-3">
            <div class="flex w-full flex-col items-stretch gap-2 sm:flex-row sm:items-start">
                <div class="min-w-0 flex-1">
                    <input type="text" wire:model="name" placeholder="Seu Nome" required style="height: 2.75rem; margin: 0;" class="block w-full rounded-control border border-border !bg-card px-3.5 text-sm text-foreground placeholder:text-muted-foreground transition-shadow focus:border-primary-500/60 focus:outline-none focus:ring-4 focus:ring-primary-500/15 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('name') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="min-w-0 flex-1">
                    <input type="email" wire:model="email" placeholder="Seu melhor e-mail" required style="height: 2.75rem; margin: 0;" class="block w-full rounded-control border border-border !bg-card px-3.5 text-sm text-foreground placeholder:text-muted-foreground transition-shadow focus:border-primary-500/60 focus:outline-none focus:ring-4 focus:ring-primary-500/15 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('email') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" style="height: 2.75rem; margin: 0; min-width: 9rem;" class="btn btn-primary shrink-0">
                    <span wire:loading.remove wire:target="subscribe" class="inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                        Inscrever
                    </span>
                    <span wire:loading wire:target="subscribe">
                        <svg class="h-4 w-4 animate-spin" width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                </button>
            </div>
            @if($turnstileEnabled)
                <div wire:ignore>
                    <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onTurnstileSuccessNewsletter"></div>
                </div>
                @error('turnstileToken') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                <script>
                    function onTurnstileSuccessNewsletter(token) {
                        @this.set('turnstileToken', token);
                    }
                </script>
            @endif
        </form>
    @endif
</div>
