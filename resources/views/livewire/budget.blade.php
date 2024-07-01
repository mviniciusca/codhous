@props(['code' => null])
<div>
    <form wire:submit='create'>
        {{ $this->form }}
        <x-ui.button>
            Enviar
        </x-ui.button>
    </form>
</div>