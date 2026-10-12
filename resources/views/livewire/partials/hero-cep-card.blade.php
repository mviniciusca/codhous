@php
    $website = \App\Models\Setting::get('website', []);
    $whatsapp = data_get($website, 'features.whatsapp_widget', []);
    $whatsappNumber = preg_replace('/\D/', '', data_get($whatsapp, 'number', ''));
    if ($whatsappNumber && strlen($whatsappNumber) <= 11) {
        $whatsappNumber = '55' . $whatsappNumber;
    }
    if (empty($whatsappNumber)) {
        $whatsappNumber = '5511999999999';
    }

    // corporate = cartão claro sobre fundo claro; padrão = vidro sobre a mídia do hero
    $isLight = isset($theme) && $theme === 'corporate';
    $cardClass = $isLight
        ? 'surface-card text-scheme-light shadow-[0_20px_50px_-30px_rgb(0_0_0/0.35)]'
        : 'dark rounded-card border border-white/15 bg-white/[0.07] shadow-2xl backdrop-blur-xl';
    $waText = rawurlencode("Olá! Verifiquei que vocês atendem minha região ({$cep}). Preciso de concreto usinado para minha obra.");
@endphp
<div class="{{ $cardClass }} p-6 md:p-7">
    <div class="mb-5 flex items-center gap-3">
        <span wire:ignore class="icon-box"><i data-lucide="map-pin"></i></span>
        <div>
            <h3 class="font-mono text-lg font-bold text-foreground">Atendemos sua região?</h3>
            <p class="text-xs text-muted-foreground">Digite o CEP da obra para verificar</p>
        </div>
    </div>

    @if(!$success)
        <div id="hero-cep-form">
            <div class="flex gap-2 rounded-control border border-border bg-card p-1.5 transition-shadow focus-within:border-primary-500/60 focus-within:ring-4 focus-within:ring-primary-500/15">
                <x-ui.input
                    wire:model.live.debounce.300ms="cep"
                    wire:keydown.enter="lookupCep"
                    id="hero-cep-input"
                    type="text"
                    inputmode="numeric"
                    maxlength="9"
                    placeholder="00000-000"
                    aria-label="CEP da obra"
                    class="flex-1 !border-0 !bg-transparent font-mono text-lg !text-foreground !shadow-none placeholder:text-muted-foreground/60 focus:!ring-0"
                />
                <button wire:click="lookupCep" id="hero-cep-btn" type="button" class="btn btn-primary !mt-0 px-4" aria-label="Verificar CEP">
                    <span wire:loading.remove wire:target="lookupCep"><i data-lucide="search" class="h-5 w-5"></i></span>
                    <span wire:loading wire:target="lookupCep"><i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i></span>
                </button>
            </div>
            @if($error)
                <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-500"><i data-lucide="circle-alert" class="h-3.5 w-3.5"></i>{{ $error }}</p>
            @endif
        </div>
    @endif

    @if($success)
        <div id="hero-cep-success">
            <div class="rounded-control border border-emerald-500/30 bg-emerald-500/10 p-4">
                <div class="mb-1 flex items-center gap-2">
                    <span wire:ignore class="flex"><i data-lucide="circle-check" class="h-5 w-5 text-emerald-500"></i></span>
                    <span class="text-sm font-semibold text-foreground">Atendemos sua região!</span>
                </div>
                <p class="text-sm text-muted-foreground">{{ $addressText }}</p>
            </div>
            <div class="mt-4 grid gap-2">
                <a id="hero-whatsapp-link" href="https://wa.me/{{ $whatsappNumber }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp w-full">
                    <i data-lucide="message-circle"></i> Falar pelo WhatsApp
                </a>
                <a href="#orcamento" class="btn btn-outline w-full">
                    Solicitar orçamento online <span wire:ignore class="flex"><i data-lucide="arrow-right" class="h-4 w-4"></i></span>
                </a>
            </div>
            <button wire:click="resetCepForm" type="button" class="!mt-3 w-full text-center text-xs text-muted-foreground transition-colors hover:text-foreground">
                Consultar outro CEP
            </button>
        </div>
    @endif

    <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-border/60 pt-4 text-xs text-muted-foreground">
        <span class="flex items-center gap-1.5"><span wire:ignore class="flex"><i data-lucide="shield-check" class="h-3.5 w-3.5 text-primary-ink"></i></span> Consulta gratuita</span>
        <span class="flex items-center gap-1.5"><span wire:ignore class="flex"><i data-lucide="clock" class="h-3.5 w-3.5 text-primary-ink"></i></span> Resposta em minutos</span>
    </div>
</div>
