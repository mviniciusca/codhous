@php
    $company = \App\Models\Setting::get('company', []);
    $companyPhone = data_get($company, 'phone', '');
    $companyPhoneDigits = $companyPhone ? preg_replace('/\D/', '', $companyPhone) : '';
    $companyPhoneTel = $companyPhoneDigits && in_array(strlen($companyPhoneDigits), [10, 11], true)
        ? '55' . $companyPhoneDigits
        : $companyPhoneDigits;
@endphp
<section id="orcamento" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">
            <div class="grid lg:grid-cols-[minmax(0,20rem)_1fr]">
                <!-- Informações (coluna mais enxuta) -->
                <div class="bg-foreground p-6 lg:p-8">
                    <h2 class="font-mono text-2xl font-bold tracking-tight text-background md:text-3xl" style="text-wrap: balance;">Solicite seu orçamento</h2>
                    <p class="mt-3 text-background/70 text-sm md:text-base">Preencha os passos abaixo. Nossa equipe analisa e retorna em até 24 horas com uma proposta personalizada.</p>

                    @if($companyPhone)
                    <p class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-background/80">
                        <i data-lucide="phone" class="h-4 w-4 shrink-0 text-primary"></i>
                        <span class="text-background/60">Prefere falar? Ligue:</span>
                        @if($companyPhoneTel)
                        <a href="tel:{{ $companyPhoneTel }}" class="font-medium text-background underline-offset-2 hover:underline">{{ $companyPhone }}</a>
                        @else
                        <span class="font-medium">{{ $companyPhone }}</span>
                        @endif
                    </p>
                    @endif

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

                <!-- Formulário (mais espaço) -->
                <div class="min-w-0 p-6 lg:p-10">
                    <livewire:budget />
                </div>
            </div>
        </div>
    </div>
</section>
