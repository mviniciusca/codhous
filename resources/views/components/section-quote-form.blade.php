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
                    <form id="quote-form" action="#" class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <x-ui.label for="name">Nome Completo</x-ui.label>
                                <x-ui.input type="text" id="name" name="name" required placeholder="Seu nome" />
                            </div>
                            <div>
                                <x-ui.label for="phone">Telefone / WhatsApp</x-ui.label>
                                <x-ui.input type="tel" id="phone" name="phone" required placeholder="(11) 99999-9999" />
                            </div>
                        </div>

                        <div>
                            <label for="service-type" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Tipo de Serviço</label>
                            <div class="grid gap-2 sm:grid-cols-3">
                                <label class="relative flex cursor-pointer items-center justify-center rounded-md border border-border px-3 py-3 transition-all hover:bg-muted focus:outline-none has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary">
                                    <input type="radio" name="service" value="concreto" class="sr-only" checked onchange="toggleFormFields('concreto')">
                                    <span class="text-xs font-medium uppercase tracking-wider">Concreto</span>
                                </label>
                                <label class="relative flex cursor-pointer items-center justify-center rounded-md border border-border px-3 py-3 transition-all hover:bg-muted focus:outline-none has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary">
                                    <input type="radio" name="service" value="bombas" class="sr-only" onchange="toggleFormFields('bombas')">
                                    <span class="text-xs font-medium uppercase tracking-wider">Bombas</span>
                                </label>
                                <label class="relative flex cursor-pointer items-center justify-center rounded-md border border-border px-3 py-3 transition-all hover:bg-muted focus:outline-none has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary">
                                    <input type="radio" name="service" value="locacao" class="sr-only" onchange="toggleFormFields('locacao')">
                                    <span class="text-xs font-medium uppercase tracking-wider">Locação</span>
                                </label>
                            </div>
                        </div>

                        <div id="concreto-fields" class="flex flex-col gap-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <x-ui.label for="volume">Volume Estimado</x-ui.label>
                                    <div class="relative flex items-center">
                                        <x-ui.input type="number" id="volume" name="volume" placeholder="Ex: 5"
                                            class="pr-10 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" />
                                        <span class="absolute right-4 text-xs font-bold text-muted-foreground">m&sup3;</span>
                                    </div>
                                </div>
                                <div>
                                    <x-ui.label for="fck">Resistência (FCK)</x-ui.label>
                                    <x-ui.select id="fck" name="fck">
                                        <option value="20">FCK 20 MPa</option>
                                        <option value="25" selected>FCK 25 MPa</option>
                                        <option value="30">FCK 30 MPa</option>
                                        <option value="35">FCK 35 MPa</option>
                                        <option value="outro">Outro traço</option>
                                    </x-ui.select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-ui.label for="address">Endereço da Obra</x-ui.label>
                            <x-ui.input type="text" id="address" name="address" required placeholder="Rua, número e bairro" />
                        </div>

                        <div>
                            <x-ui.label for="obs">Observações Adicionais</x-ui.label>
                            <x-ui.textarea id="obs" name="obs" rows="3" placeholder="Conte mais detalhes sobre sua necessidade..."></x-ui.textarea>
                        </div>

                        <button type="submit" class="group mt-2 inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 text-base font-semibold text-primary-foreground transition-all hover:bg-primary/90">
                            Enviar Solicitação <i data-lucide="send" class="h-5 w-5 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
