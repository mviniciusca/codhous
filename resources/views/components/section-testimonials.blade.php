<section id="depoimentos" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Depoimentos</span>
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">O que dizem nossos clientes</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">Empresas e obras que confiam na nossa entrega e no nosso suporte.</p>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <div class="flex flex-col rounded-xl border border-border bg-card p-8 shadow-sm transition-all hover:border-primary/30 hover:shadow-md">
                <div class="mb-6 flex gap-1 text-primary">
                    @foreach(range(1, 5) as $i)
                        <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                    @endforeach
                </div>
                <p class="flex-1 text-sm leading-relaxed text-muted-foreground">"Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível. Já fechamos várias obras com eles."</p>
                <div class="mt-6 flex items-center gap-4 border-t border-border pt-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <i data-lucide="user" class="h-6 w-6 text-primary"></i>
                    </div>
                    <div>
                        <p class="font-mono text-sm font-bold text-foreground">Carlos Mendes</p>
                        <p class="text-xs text-muted-foreground">Engenheiro — Obra Residencial Alphaville</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-xl border border-border bg-card p-8 shadow-sm transition-all hover:border-primary/30 hover:shadow-md">
                <div class="mb-6 flex gap-1 text-primary">
                    @foreach(range(1, 5) as $i)
                        <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                    @endforeach
                </div>
                <p class="flex-1 text-sm leading-relaxed text-muted-foreground">"Pontualidade e qualidade do traço fazem a diferença. Nosso cronograma não atrasa por causa de concreto."</p>
                <div class="mt-6 flex items-center gap-4 border-t border-border pt-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <i data-lucide="user" class="h-6 w-6 text-primary"></i>
                    </div>
                    <div>
                        <p class="font-mono text-sm font-bold text-foreground">Ana Paula Costa</p>
                        <p class="text-xs text-muted-foreground">Mestre de obras — Construtora regional</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-xl border border-border bg-card p-8 shadow-sm transition-all hover:border-primary/30 hover:shadow-md md:col-span-2 lg:col-span-1">
                <div class="mb-6 flex gap-1 text-primary">
                    @foreach(range(1, 5) as $i)
                        <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                    @endforeach
                </div>
                <p class="flex-1 text-sm leading-relaxed text-muted-foreground">"Orçamento claro, entrega no horário e suporte pós-venda. Recomendo para quem não pode perder tempo em obra."</p>
                <div class="mt-6 flex items-center gap-4 border-t border-border pt-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <i data-lucide="user" class="h-6 w-6 text-primary"></i>
                    </div>
                    <div>
                        <p class="font-mono text-sm font-bold text-foreground">Roberto Lima</p>
                        <p class="text-xs text-muted-foreground">Arquiteto — Escritório próprio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
