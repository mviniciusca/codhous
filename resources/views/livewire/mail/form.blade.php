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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground" for="name">
                        Nome Completo <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" placeholder="Seu nome completo" class="flex h-10 w-full rounded-md border border-input !bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('name') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground" for="email">
                        E-mail <span class="text-danger-600">*</span>
                    </label>
                    <input type="email" id="email" wire:model="email" placeholder="seu@email.com" class="flex h-10 w-full rounded-md border border-input !bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    @error('email') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground" for="phone">
                    Telefone <span class="text-danger-600">*</span>
                </label>
                <div x-data>
                    <input type="tel" id="phone" x-mask="(99) 99999-9999" wire:model="phone" placeholder="(21) 90000-0000" class="flex h-10 w-full rounded-md border border-input !bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                </div>
                @error('phone') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground" for="subject">
                    Assunto <span class="text-danger-600">*</span>
                </label>
                <select id="subject" wire:model="subject" class="flex h-10 w-full rounded-md border border-input !bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    <option value="Dúvida">Dúvida</option>
                    <option value="Elogio">Elogio</option>
                    <option value="Outro">Outro</option>
                </select>
                @error('subject') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground" for="message">
                    Mensagem <span class="text-danger-600">*</span>
                </label>
                <textarea id="message" wire:model="message" rows="4" placeholder="Como podemos ajudar?" class="flex w-full rounded-md border border-input !bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                @error('message') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
            </div>

            {{-- Cloudflare Turnstile --}}
            @if($turnstileEnabled)
                <div class="space-y-2">
                    <div 
                        wire:ignore
                        class="cf-turnstile" 
                        data-sitekey="{{ $turnstileSiteKey }}"
                        data-callback="onTurnstileSuccess"
                        data-theme="light"
                        data-size="flexible"
                    ></div>
                    @error('turnstileToken')
                        <p class="text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <button type="submit" class="w-full rounded-lg bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                <span wire:loading.remove>Enviar Mensagem</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>

        @if($turnstileEnabled)
            @push('scripts')
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                <script>
                    function onTurnstileSuccess(token) {
                        @this.set('turnstileToken', token);
                    }
                </script>
            @endpush
        @endif
    @endif
</div>
