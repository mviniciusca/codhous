@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'badge' => null,
    'title' => null,
    'description' => null,
    'whatsappEnabled' => true,
    'callEnabled' => true,
    'emailEnabled' => true,
])

@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $company = \App\Models\Setting::get('company', []);
    $website = \App\Models\Setting::get('website', []);

    $whatsappNumber = data_get($website, 'features.whatsapp_widget.number');
    $whatsappMsg = data_get($website, 'features.whatsapp_widget.message', 'Olá! Gostaria de um orçamento.');
    $whatsappUrl = $whatsappNumber ? 'https://wa.me/55' . preg_replace('/[^0-9]/', '', $whatsappNumber) . '?text=' . urlencode($whatsappMsg) : null;
    $phone = data_get($company, 'phone');
    $email = data_get($company, 'email');
@endphp

@if(!\App\Models\ContentSection::isHidden('contact-banner'))
<x-ui.section :data="$data" spacing="sm" background="bg-background">
    <div class="surface-card relative isolate overflow-hidden p-6 md:p-10" data-spotlight data-reveal>
        <div class="absolute -right-16 -top-24 -z-10 h-64 w-64 rounded-full bg-primary-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 -z-10 h-48 w-48 rounded-full bg-primary-400/10 blur-3xl"></div>

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="max-w-xl">
                <span class="kicker mb-3"><span class="kicker-dot"></span> {{ $badge ?: 'Atendimento' }}</span>
                <h2 class="font-mono text-2xl font-bold tracking-tight text-foreground md:text-3xl" style="text-wrap: balance;">{{ $title ?: 'Fale conosco' }}</h2>
                <p class="mt-2 text-base leading-relaxed text-muted-foreground">{{ $description ?: 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.' }}</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($whatsappEnabled && $whatsappUrl)
                    <x-ui.button :href="$whatsappUrl" target="_blank" variant="whatsapp" icon="message-circle" trailing-icon="arrow-right">WhatsApp</x-ui.button>
                @endif
                @if($callEnabled && $phone)
                    <x-ui.button :href="'tel:' . preg_replace('/[^0-9]/', '', $phone)" variant="secondary" icon="phone">Ligar</x-ui.button>
                @endif
                @if($emailEnabled && $email)
                    <x-ui.button :href="'mailto:' . $email" variant="secondary" icon="mail">E-mail</x-ui.button>
                @endif
            </div>
        </div>
    </div>
</x-ui.section>
@endif
