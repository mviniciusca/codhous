<section id="orcamento" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">
            <div class="grid lg:grid-cols-2">
                <!-- Informações -->
                <div class="bg-foreground p-8 lg:p-12">
                    <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Atendimento Rápido</span>
                    <h2 class="font-mono text-3xl font-bold tracking-tight text-background md:text-4xl" style="text-wrap: balance;">Pronto para iniciar seu projeto?</h2>
                    <p class="mt-4 text-background/60">Nossa equipe está pronta para fornecer o melhor orçamento para sua obra. Resposta em até 2 horas em horário comercial.</p>

                    <div class="mt-12 flex flex-col gap-8">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-background/5">
                                <i data-lucide="check-circle" class="h-6 w-6 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-mono text-lg font-bold text-background">Traço Preciso</h4>
                                <p class="text-sm text-background/40">Garantia de resistência e qualidade em metros cúbicos exatos.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-background/5">
                                <i data-lucide="truck" class="h-6 w-6 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-mono text-lg font-bold text-background">Entrega Programada</h4>
                                <p class="text-sm text-background/40">Logística avançada para entrega no horário combinado.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-background/5">
                                <i data-lucide="shield-check" class="h-6 w-6 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-mono text-lg font-bold text-background">Segurança Total</h4>
                                <p class="text-sm text-background/40">Seguimos todas as normas técnicas (ABNT) rigorosamente.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulário -->
                <div class="p-8 lg:p-12">
                    <livewire:budget />
                </div>
            </div>
        </div>
    </div>
</section>
