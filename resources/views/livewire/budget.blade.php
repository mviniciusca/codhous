@props(['code' => null])
<div>
    <form wire:submit='create'>
        {{ $this->form }}
        <x-ui.button>
            {{ __('Send') }}
        </x-ui.button>
    </form>
</div>