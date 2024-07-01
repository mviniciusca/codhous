@props(['code' => null])
<div>
    <form wire:submit='create'>
        {{ $this->form }}
        <input name="code" type="hidden" value="{{ $code }}">
        <x-ui.button>
            Enviar
        </x-ui.button>
    </form>
</div>