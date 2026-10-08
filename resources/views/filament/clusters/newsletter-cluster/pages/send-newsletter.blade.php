<x-filament-panels::page>
    <x-filament-panels::form wire:submit="sendMessages">
        {{ $this->form }}

        <x-filament-panels::form.actions 
            :actions="$this->getFormActions()"
            class="mt-4"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
