<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3 mb-4">
            <x-filament::icon
                icon="heroicon-o-chart-pie"
                class="h-5 w-5 text-gray-500 dark:text-gray-400"
            />
            <div class="flex-1">
                <h2 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                    Orçamentos por Status
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Distribuição dos orçamentos no período.
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-y-4">
            <!-- Pendente -->
            <div class="flex items-center gap-x-3">
                <div class="h-2.5 w-2.5 rounded-full bg-orange-500"></div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Pendente</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $pending }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-orange-500" style="width: {{ $pendingPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $pendingPercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Em análise -->
            <div class="flex items-center gap-x-3">
                <div class="h-2.5 w-2.5 rounded-full bg-blue-500"></div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Em análise</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $onGoing }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $onGoingPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $onGoingPercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Aprovado -->
            <div class="flex items-center gap-x-3">
                <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Aprovado</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $done }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $donePercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $donePercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Rejeitado -->
            <div class="flex items-center gap-x-3">
                <div class="h-2.5 w-2.5 rounded-full bg-rose-500"></div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Rejeitado</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $ignored }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-rose-500" style="width: {{ $ignoredPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $ignoredPercent }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
