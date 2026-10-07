<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3 mb-4">
            <x-filament::icon
                icon="heroicon-o-truck"
                class="h-5 w-5 text-gray-500 dark:text-gray-400"
            />
            <div class="flex-1">
                <h2 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                    Disponibilidade de Equipamentos
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Status da frota para locação.
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-y-4">
            <!-- Disponíveis -->
            <div class="flex items-center gap-x-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10">
                    <x-filament::icon icon="heroicon-m-shield-check" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Disponíveis</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $available }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $availablePercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $availablePercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Alugados -->
            <div class="flex items-center gap-x-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                    <x-filament::icon icon="heroicon-m-clock" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Alugados</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $rented }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $rentedPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $rentedPercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Manutenção -->
            <div class="flex items-center gap-x-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-500/10">
                    <x-filament::icon icon="heroicon-m-wrench" class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Manutenção</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $maintenance }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-orange-500" style="width: {{ $maintenancePercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $maintenancePercent }}%</p>
                    </div>
                </div>
            </div>

            <!-- Indisponíveis -->
            <div class="flex items-center gap-x-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-500/10">
                    <x-filament::icon icon="heroicon-m-x-circle" class="h-5 w-5 text-rose-600 dark:text-rose-400" />
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Indisponíveis</p>
                        <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $unavailable }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-rose-500" style="width: {{ $unavailablePercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">{{ $unavailablePercent }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
