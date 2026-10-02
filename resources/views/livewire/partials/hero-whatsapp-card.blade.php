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
    $cardBg = $isLight ? 'bg-zinc-50 border-zinc-200 shadow-sm' : 'border-background/10 bg-background/5 backdrop-blur-sm';
    $titleColor = $isLight ? 'text-zinc-900' : 'text-background';
    $subtitleColor = $isLight ? 'text-zinc-500' : 'text-background/50';
@endphp
<div class="rounded-xl border p-8 {{ $cardBg }}">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10">
            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </div>
        <div>
            <h3 class="font-mono text-xl font-bold {{ $titleColor }}">Fale Conosco</h3>
            <p class="text-sm {{ $subtitleColor }}">Escolha como prefere ser atendido</p>
        </div>
    </div>

    <div class="flex flex-col gap-4">
        {{-- WhatsApp Button --}}
        <a href="https://wa.me/{{ $whatsappNumber }}?text=Ol%C3%A1!%20Vim%20pelo%20site%20e%20gostaria%20de%20um%20or%C3%A7amento." target="_blank" rel="noopener noreferrer"
            class="group relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-xl bg-[#25D366] px-6 py-4 text-base font-bold text-white shadow-lg shadow-[#25D366]/25 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#25D366]/40">
            <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-150%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(150%)]">
                <div class="relative h-full w-8 bg-white/20"></div>
            </div>
            <span class="relative z-10 flex items-center gap-3">
                <svg class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Iniciar Conversa
            </span>
        </a>

        {{-- Primary Action Button --}}
        <a href="#orcamento" class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-primary px-6 py-4 text-sm font-bold text-white shadow-lg shadow-primary/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/40">
            <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-150%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(150%)]">
                <div class="relative h-full w-8 bg-white/20"></div>
            </div>
            <span class="relative z-10 flex items-center gap-2">
                Solicitar Orçamento Online
                <span wire:ignore class="flex items-center justify-center transition-transform duration-300 group-hover:translate-x-1"><i data-lucide="arrow-right" class="h-4 w-4"></i></span>
            </span>
        </a>
    </div>
</div>
