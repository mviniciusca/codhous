@php
    $section = \App\Models\ContentSection::getBySlug('cta_contact');
    $ctaTitle = $section?->content['title'] ?? 'Fale conosco';
    $ctaSubtitle = $section?->content['subtitle'] ?? 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.';
    $company = \App\Models\Setting::get('company', []);
    $companyPhone = data_get($company, 'phone', '(11) 99999-9999');
    $companyEmail = data_get($company, 'email', 'contato@exemplo.com.br');
    $website = \App\Models\Setting::get('website', []);
    $whatsapp = data_get($website, 'features.whatsapp_widget', []);
    $whatsappNumber = data_get($whatsapp, 'number', '5511999999999');
    $whatsappNumberClean = preg_replace('/\D/', '', $whatsappNumber);
@endphp

<section id="fale-conosco" class="bg-foreground py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="flex flex-col items-center gap-10 rounded-2xl border border-background/10 bg-background/5 px-6 py-12 text-center backdrop-blur-sm lg:flex-row lg:justify-between lg:gap-8 lg:px-12 lg:py-10 lg:text-left">
            <div class="max-w-xl">
                <span class="mb-3 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Atendimento</span>
                <h2 class="font-mono text-2xl font-bold tracking-tight text-background md:text-3xl" style="text-wrap: balance;">{{ $ctaTitle }}</h2>
                <p class="mt-3 text-background/60">{{ $ctaSubtitle }}</p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4 lg:gap-6">
                <a href="https://wa.me/{{ $whatsappNumberClean }}?text=Olá! Gostaria de mais informações sobre concreto usinado."
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 rounded-lg bg-[#25D366] px-6 py-4 text-sm font-semibold text-white transition-all hover:bg-[#1fb855]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                <a href="tel:{{ preg_replace('/\D/', '', $companyPhone) }}"
                   class="inline-flex items-center gap-3 rounded-lg border border-background/20 bg-background/5 px-6 py-4 text-sm font-semibold text-background transition-colors hover:bg-background/10">
                    <i data-lucide="phone" class="h-5 w-5"></i>
                    Ligar
                </a>
                <a href="mailto:{{ $companyEmail }}"
                   class="inline-flex items-center gap-3 rounded-lg border border-background/20 bg-background/5 px-6 py-4 text-sm font-semibold text-background transition-colors hover:bg-background/10">
                    <i data-lucide="mail" class="h-5 w-5"></i>
                    E-mail
                </a>
            </div>
        </div>
    </div>
</section>
