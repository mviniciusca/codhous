<div class="budget-wizard-wrap">
    <style>
        /* Scroll “sobe” mais para o passo ficar abaixo do menu fixo (navegador em direção ao header) */
        .budget-wizard-wrap .fi-fo-wizard-header-step,
        .budget-wizard-wrap .fi-fo-wizard-step {
            scroll-margin-top: 10rem;
        }

        /* Botão Próximo visível (fundo e texto) – override do tema */
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(1) .fi-btn {
            background-color: rgb(229 231 235) !important;
            color: rgb(17 24 39) !important;
        }
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(1) .fi-btn:hover {
            opacity: 0.9;
        }
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(3) .fi-btn,
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(4) .fi-btn {
            background-color: #dc2626 !important; /* primary visível em fundo branco */
            color: #fff !important;
        }
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(3) .fi-btn:hover,
        .budget-wizard-wrap .fi-fo-wizard div.flex.items-center.justify-between span:nth-child(4) .fi-btn:hover {
            opacity: 0.9;
        }
    </style>
    @if(!$isSubmitted)
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-gray-900/50 sm:p-8">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Solicite seu orçamento
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 sm:text-base">
                    Preencha os passos abaixo. Nossa equipe analisa e retorna em até 24 horas com uma proposta personalizada.
                </p>
            </div>
            <form wire:submit.prevent class="flex flex-col gap-6">
                {{ $this->form }}
            </form>
        </div>
    @else
        <div class="flex flex-col items-center justify-center p-8 rounded-xl bg-background/5 border border-background/10 space-y-6 animate-in fade-in zoom-in duration-700">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary">
                <span wire:ignore>
                    <!-- inline check SVG (Lucide style) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </div>
            
            <div class="text-center space-y-3">
                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground">
                    Muito <span class="text-primary">Obrigado!</span>
                </h2>
                <p class="text-lg leading-relaxed text-foreground/70">
                    Sua solicitação de orçamento foi recebida com sucesso.
                </p>
                
                <div class="inline-block rounded-full border border-primary/30 bg-primary/10 px-4 py-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-primary">Retorno em até 24 horas</p>
                </div>
            </div>

            <div class="pt-4 w-full">
                <button wire:click="resetForm" class="flex w-full items-center justify-center gap-2 rounded-md border border-primary/20 bg-primary/10 px-6 py-4 text-xs font-semibold uppercase tracking-widest text-primary transition-colors hover:bg-primary/20 hover:text-primary-foreground focus:outline-none">
                    <span wire:ignore><i data-lucide="rotate-ccw" class="h-4 w-4 text-primary"></i></span>
                    Nova Solicitação
                </button>
            </div>
        </div>
    @endif
</div>