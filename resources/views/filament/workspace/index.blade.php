<x-filament-panels::page class="fi-workspace-page">
    @include('filament.dashboard.styles')
    @include('filament.workspace.styles')

    @php
        $today = \App\Support\WorkspaceDates::today();
        $badges = [
            'tarefas' => $this->taskCounts['pending'],
            'agenda' => null,
            'notas' => $this->noteCount,
        ];
    @endphp

    <div class="ws cx-ui">
        <x-filament::tabs label="Áreas do workspace" class="ws-nav">
            @foreach (\App\Filament\Pages\Workspace::TABS as $key => $item)
                <x-filament::tabs.item
                    :active="$tab === $key"
                    :icon="$item['icon']"
                    :badge="$badges[$key] ?: null"
                    wire:click="$set('tab', '{{ $key }}')"
                >
                    {{ $item['label'] }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        @switch($tab)
            @case('agenda')
                @include('filament.workspace.agenda')
                @break
            @case('notas')
                @include('filament.workspace.notes')
                @break
            @default
                @include('filament.workspace.tasks')
        @endswitch
    </div>
</x-filament-panels::page>
