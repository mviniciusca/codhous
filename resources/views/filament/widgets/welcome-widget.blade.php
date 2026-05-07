<div class="fi-wi-widget">
    <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex flex-col gap-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Olá, {{ $this->getUserName() }}, {{ $this->getGreeting() }}!
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Bem-vindo de volta ao seu painel de controle. Aqui está o que está acontecendo hoje.
            </p>
        </div>
    </div>
</div>
