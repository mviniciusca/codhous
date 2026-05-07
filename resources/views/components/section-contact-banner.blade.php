@props([
    'badge' => 'ATENDIMENTO',
    'title' => 'Fale conosco',
    'description' => 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.',
    'whatsappEnabled' => true,
    'callEnabled' => true,
    'emailEnabled' => true,
])

@php
    $company = \App\Models\Setting::get('company', []);
    $website = \App\Models\Setting::get('website', []);
    
    // WhatsApp
    $whatsappNumber = data_get($website, 'features.whatsapp_widget.number');
    $whatsappMsg = data_get($website, 'features.whatsapp_widget.message', 'Olá! Gostaria de um orçamento.');
    $whatsappUrl = $whatsappNumber 
        ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode($whatsappMsg)
        : '#';

    // Telefone
    $phone = data_get($company, 'phone');
    $phoneUrl = $phone ? "tel:" . preg_replace('/[^0-9]/', '', $phone) : '#';

    // E-mail
    $email = data_get($company, 'email');
    $emailUrl = $email ? "mailto:" . $email : '#';
@endphp

<section class="bg-background py-6">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-border bg-card p-8 shadow-sm transition-all hover:shadow-md md:p-10">
            {{-- Efeito de gradiente sutil no fundo --}}
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>

            <div class="relative flex flex-col items-center justify-between gap-8 lg:flex-row">
                {{-- Conteúdo Esquerdo --}}
                <div class="flex-1 text-left">
                    @if($badge)
                        <span class="mb-3 inline-block text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                            {{ $badge }}
                        </span>
                    @endif
                    
                    <h2 class="font-mono text-2xl font-bold tracking-tight text-foreground md:text-3xl" style="text-wrap: balance;">
                        {{ $title }}
                    </h2>
                    
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-muted-foreground">
                        {{ $description }}
                    </p>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex flex-wrap items-center justify-center gap-3 md:gap-4">
                    @if($whatsappEnabled)
                        <a href="{{ $whatsappUrl }}" target="_blank" class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#25D366] px-6 text-sm font-bold text-white transition-all hover:scale-105 hover:bg-[#20ba5a] hover:shadow-lg active:scale-95">
                            <i data-lucide="message-circle" class="h-5 w-5 fill-current"></i>
                            WhatsApp
                        </a>
                    @endif

                    @if($callEnabled)
                        <a href="{{ $phoneUrl }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-border bg-background px-6 text-sm font-bold text-foreground transition-all hover:bg-muted hover:shadow-sm active:scale-95">
                            <i data-lucide="phone" class="h-4 w-4 text-muted-foreground"></i>
                            Ligar
                        </a>
                    @endif

                    @if($emailEnabled)
                        <a href="{{ $emailUrl }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-border bg-background px-6 text-sm font-bold text-foreground transition-all hover:bg-muted hover:shadow-sm active:scale-95">
                            <i data-lucide="mail" class="h-4 w-4 text-muted-foreground"></i>
                            E-mail
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
