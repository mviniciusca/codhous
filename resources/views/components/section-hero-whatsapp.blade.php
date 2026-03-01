<section class="relative flex min-h-[90vh] items-center overflow-hidden bg-foreground pt-16">
    {{-- Concrete texture pattern --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%224%22 height=%224%22><rect width=%224%22 height=%224%22 fill=%22%23fff%22/><rect width=%221%22 height=%221%22 fill=%22%23ccc%22/></svg>'); background-size: 4px 4px;"></div>

    {{-- Grid lines --}}
    <div class="pointer-events-none absolute inset-0 opacity-5">
        <div class="absolute left-1/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-background"></div>
    </div>

    <div class="relative mx-auto w-full max-w-4xl px-4 py-20 text-center lg:px-8">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
            <span class="h-2 w-2 rounded-full bg-primary"></span>
            <span class="text-xs font-medium uppercase tracking-wider text-primary">Atendimento Imediato</span>
        </div>

        <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
            Concreto usinado na sua obra em ate <span class="text-primary">2 horas</span>
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-background/60">
            Fale diretamente com nosso time comercial pelo WhatsApp e receba seu orcamento em minutos. Atendimento rapido, sem burocracia.
        </p>

        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="https://wa.me/5511999999999?text=Ola!%20Preciso%20de%20concreto%20usinado%20para%20minha%20obra." target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-3 rounded-md bg-[#25D366] px-8 py-4 text-base font-semibold text-white transition-all hover:bg-[#1fb855] hover:gap-4">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Fale pelo WhatsApp Agora
            </a>
            <a href="#orcamento" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 text-base font-semibold text-primary-foreground transition-all hover:bg-primary/90 hover:gap-3">
                Solicitar Orcamento
                <i data-lucide="arrow-right" class="h-5 w-5"></i>
            </a>
        </div>

        {{-- Stats --}}
        <div class="mx-auto mt-14 flex max-w-lg flex-wrap items-center justify-center gap-6 lg:gap-10">
            <div class="text-center">
                <p class="font-mono text-2xl font-bold text-primary">500+</p>
                <p class="text-xs text-background/50">Obras atendidas</p>
            </div>
            <div class="h-8 w-px bg-background/10"></div>
            <div class="text-center">
                <p class="font-mono text-2xl font-bold text-background">98%</p>
                <p class="text-xs text-background/50">Pontualidade</p>
            </div>
            <div class="h-8 w-px bg-background/10"></div>
            <div class="text-center">
                <p class="font-mono text-2xl font-bold text-background">15+</p>
                <p class="text-xs text-background/50">Anos no mercado</p>
            </div>
        </div>
    </div>
</section>
