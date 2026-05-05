@php
    use App\Models\Setting;
    $whatsapp = Setting::get('website.social_networks.whatsapp') ?? '#';
    $phone = Setting::get('company.phone');
    $email = Setting::get('company.email');
@endphp

<section class="bg-background py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-card border border-border p-8 md:p-12">
            {{-- Background decorative elements --}}
            <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute -left-24 -bottom-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>

            <div class="relative flex flex-col items-center justify-between gap-8 lg:flex-row">
                <div class="max-w-2xl text-center lg:text-left">
                    <span class="mb-4 inline-block text-[10px] font-bold uppercase tracking-widest text-primary">
                        Atendimento
                    </span>
                    <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl">
                        Fale conosco
                    </h2>
                    <p class="mt-4 text-lg leading-relaxed text-muted-foreground">
                        Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.
                    </p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3 lg:justify-end">
                    <a href="{{ $whatsapp }}" target="_blank" class="inline-flex min-w-[140px] items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 py-3.5 text-sm font-bold text-white transition-all hover:scale-105 hover:shadow-lg">
                        <i data-lucide="message-circle" class="h-4 w-4"></i>
                        WhatsApp
                    </a>
                    
                    @if($phone)
                        <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="inline-flex min-w-[120px] items-center justify-center gap-2 rounded-xl border border-border bg-background/50 px-5 py-3.5 text-sm font-bold text-foreground transition-all hover:bg-border/40 hover:scale-105">
                            <i data-lucide="phone" class="h-4 w-4"></i>
                            Ligar
                        </a>
                    @endif

                    @if($email)
                        <a href="mailto:{{ $email }}" class="inline-flex min-w-[120px] items-center justify-center gap-2 rounded-xl border border-border bg-background/50 px-5 py-3.5 text-sm font-bold text-foreground transition-all hover:bg-border/40 hover:scale-105">
                            <i data-lucide="mail" class="h-4 w-4"></i>
                            E-mail
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
