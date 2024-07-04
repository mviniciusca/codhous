@props(['code' => null])
<div class="inline-flex w-full gap-8">
    <div class="w-1/2 bg-cover rounded-md  bg-no-repeat  bg-secondary-700"
        style="background-image: url(https://www.guru.com/blog/wp-content/uploads/2023/04/civil-engineer-duties.jpg)">
    </div>
    <div class="w-1/2">
        <form wire:submit='create'>
            {{ $this->form }}
            <x-ui.button>
                {{ __('Send') }}
            </x-ui.button>
        </form>
    </div>
</div>