<div class="mt-6 sm:max-w-3xl w-full">
    <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Assine nossa Newsletter</h4>
    @if($sent)
        <div class="rounded-md bg-green-50 p-4 text-green-800 dark:bg-green-900/50 dark:text-green-300">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="h-5 w-5"></i>
                <p class="text-sm font-medium">Inscrição realizada com sucesso! Fique de olho na sua caixa de entrada.</p>
            </div>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col gap-3">
            <div class="flex w-full flex-col items-stretch gap-2 sm:flex-row sm:items-start">
                <div class="min-w-0 flex-1">
                    <input type="text" wire:model="name" placeholder="Seu Nome" required style="height: 2.75rem; margin: 0;" class="block w-full rounded-md border border-input bg-background px-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('name') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="min-w-0 flex-1">
                    <input type="email" wire:model="email" placeholder="Seu melhor e-mail" required style="height: 2.75rem; margin: 0;" class="block w-full rounded-md border border-input bg-background px-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('email') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" style="height: 2.75rem; margin: 0; min-width: 9rem;" class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-md bg-primary px-5 text-sm font-semibold text-primary-foreground ring-offset-background transition-all hover:bg-primary/90 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
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
