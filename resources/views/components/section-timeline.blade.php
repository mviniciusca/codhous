<section id="como-funciona" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Processo Simplificado</span>
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">Como funciona</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">Do orcamento a entrega, tudo pensado para facilitar sua obra.</p>
        </div>

        {{-- Desktop: horizontal timeline --}}
        <div class="hidden lg:block">
            <div class="relative">
                {{-- Connecting line --}}
                <div class="absolute left-0 right-0 top-10 h-px bg-border"></div>
                <div class="absolute left-0 top-10 h-px bg-primary" style="width: 75%;"></div>

                <div class="grid grid-cols-4 gap-8">
                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative z-10 mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="message-square-text" class="h-8 w-8 text-primary"></i>
                        </div>
                        <span class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 1</span>
                        <h3 class="font-mono text-lg font-bold text-foreground">Solicite o Orcamento</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra.</p>
                    </div>

                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative z-10 mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="clipboard-check" class="h-8 w-8 text-primary"></i>
                        </div>
                        <span class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 2</span>
                        <h3 class="font-mono text-lg font-bold text-foreground">Aprovacao do Traco</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Nossos engenheiros definem o traco ideal para a necessidade e o tipo da sua estrutura.</p>
                    </div>

                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative z-10 mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="factory" class="h-8 w-8 text-primary"></i>
                        </div>
                        <span class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 3</span>
                        <h3 class="font-mono text-lg font-bold text-foreground">Producao na Usina</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">O concreto e produzido com controle de qualidade rigoroso e rastreabilidade total.</p>
                    </div>

                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative z-10 mb-6 flex h-20 w-20 items-center justify-center rounded-full border-2 border-primary/30 bg-background">
                            <i data-lucide="truck" class="h-8 w-8 text-primary"></i>
                        </div>
                        <span class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 4</span>
                        <h3 class="font-mono text-lg font-bold text-foreground">Entrega na Obra</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Frota propria com rastreamento entrega o concreto no horario combinado.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile: vertical timeline --}}
        <div class="lg:hidden">
            <div class="relative ml-10">
                {{-- Vertical line --}}
                <div class="absolute left-0 top-0 h-full w-px bg-border"></div>

                <div class="flex flex-col gap-10">
                    <div class="relative flex gap-6">
                        <div class="absolute -left-6 top-0 z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="message-square-text" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div class="pl-10">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 1</span>
                            <h3 class="mt-1 font-mono text-lg font-bold text-foreground">Solicite o Orcamento</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Envie seu pedido pelo site, WhatsApp ou telefone com os dados da obra.</p>
                        </div>
                    </div>

                    <div class="relative flex gap-6">
                        <div class="absolute -left-6 top-0 z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="clipboard-check" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div class="pl-10">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 2</span>
                            <h3 class="mt-1 font-mono text-lg font-bold text-foreground">Aprovacao do Traco</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Nossos engenheiros definem o traco ideal para a necessidade e o tipo da sua estrutura.</p>
                        </div>
                    </div>

                    <div class="relative flex gap-6">
                        <div class="absolute -left-6 top-0 z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-primary bg-background">
                            <i data-lucide="factory" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div class="pl-10">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 3</span>
                            <h3 class="mt-1 font-mono text-lg font-bold text-foreground">Producao na Usina</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">O concreto e produzido com controle de qualidade rigoroso e rastreabilidade total.</p>
                        </div>
                    </div>

                    <div class="relative flex gap-6">
                        <div class="absolute -left-6 top-0 z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-primary/30 bg-background">
                            <i data-lucide="truck" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div class="pl-10">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-primary">Etapa 4</span>
                            <h3 class="mt-1 font-mono text-lg font-bold text-foreground">Entrega na Obra</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Frota propria com rastreamento entrega o concreto no horario combinado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
