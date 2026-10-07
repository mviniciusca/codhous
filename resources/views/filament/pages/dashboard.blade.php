<x-filament-panels::page class="fi-dashboard-page">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-2">
        <div class="flex flex-col gap-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ $this->getGreeting() }}, {{ $this->getUserName() }}!
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Aqui está um resumo do que está acontecendo na Codhous hoje.
            </p>
        </div>
        
        @if (method_exists($this, 'filtersForm'))
            <div class="mt-4 md:mt-0 w-full md:w-auto">
                {{ $this->filtersForm }}
            </div>
        @endif
    </div>

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
