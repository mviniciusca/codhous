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

    $isLight = isset($theme) && $theme === 'corporate';
    $cardClass = $isLight
        ? 'surface-card text-scheme-light shadow-[0_20px_50px_-30px_rgb(0_0_0/0.35)]'
        : 'dark rounded-card border border-white/15 bg-white/[0.07] shadow-2xl backdrop-blur-xl';
@endphp
<div class="{{ $cardClass }} p-6 md:p-7">
    <div class="mb-6 flex items-center gap-3">
        <span class="icon-box icon-box-lg"><i data-lucide="messages-square"></i></span>
        <div>
            <h3 class="font-mono text-xl font-bold text-foreground">Fale conosco</h3>
            <p class="text-sm text-muted-foreground">Escolha como prefere ser atendido</p>
        </div>
    </div>

    <div class="grid gap-3">
        <a href="https://wa.me/{{ $whatsappNumber }}?text=Ol%C3%A1!%20Vim%20pelo%20site%20e%20gostaria%20de%20um%20or%C3%A7amento." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg group w-full overflow-hidden">
            <span class="absolute inset-0 -translate-x-full skew-x-12 bg-white/20 transition-transform duration-1000 group-hover:translate-x-full" aria-hidden="true"></span>
            <i data-lucide="message-circle"></i> Iniciar conversa
        </a>
        <a href="#orcamento" class="btn btn-primary btn-lg w-full">
            Solicitar orçamento online
            <span wire:ignore class="flex" data-icon-trailing><i data-lucide="arrow-right" class="h-4 w-4"></i></span>
        </a>
    </div>
</div>
