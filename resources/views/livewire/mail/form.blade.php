<div>
    <form wire:submit="create">
        {{ $this->form }}


        <x-ui.button>
            Submit
        </x-ui.button>

    </form>

    <x-filament-actions::modals />
</div>
