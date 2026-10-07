<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3 mb-4">
            <x-filament::icon
                icon="heroicon-o-bolt"
                class="h-5 w-5 text-gray-500 dark:text-gray-400"
            />
            <h2 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                Ações Rápidas
            </h2>
        </div>

        <div class="flex flex-col gap-y-3">
            <button type="button" wire:click="mountAction('calculate')" class="w-full text-left flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                <div class="flex items-center gap-x-3">
                    <x-filament::icon icon="heroicon-o-calculator" class="h-5 w-5 text-primary-500" />
                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">Cotação Rápida</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Calculadora de orçamento</p>
                    </div>
                </div>
                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5 text-gray-400" />
            </button>

            <a href="{{ route('filament.admin.resources.budgets.create') }}" class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                <div class="flex items-center gap-x-3">
                    <x-filament::icon icon="heroicon-o-document-plus" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">Novo Orçamento</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Criar uma nova solicitação</p>
                    </div>
                </div>
                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5 text-gray-400" />
            </a>

            <a href="{{ route('filament.admin.resources.customers.create') }}" class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                <div class="flex items-center gap-x-3">
                    <x-filament::icon icon="heroicon-o-user-plus" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">Novo Cliente</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cadastrar um novo cliente</p>
                    </div>
                </div>
                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5 text-gray-400" />
            </a>

            <a href="{{ route('filament.admin.resources.equipment.create') }}" class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                <div class="flex items-center gap-x-3">
                    <x-filament::icon icon="heroicon-o-truck" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">Cadastrar Equipamento</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Adicionar à frota de locação</p>
                    </div>
                </div>
                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5 text-gray-400" />
            </a>
        </div>
        
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
