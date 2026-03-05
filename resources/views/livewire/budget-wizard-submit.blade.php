<button
    type="button"
    wire:click="create"
    class="group inline-flex items-center justify-center gap-1.5 rounded-md bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
>
    {{ __('Enviar solicitação de orçamento') }}
    <span wire:ignore class="flex items-center justify-center">
        <i data-lucide="send" class="h-4 w-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
    </span>
</button>
