@aware(['page'])
@props(['buttons' => null])

@if($buttons !== null)

<div class="mb-8 flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-x-4 sm:space-y-0 lg:mb-16">
    @foreach ($buttons as $item)
    <a target="{{ $item['target'] ? '_blank' : '_self'}}" href="{{ $item['link'] }}">
        <x-ui.button :icon="$item['icon']" :iconLeft="$item['iconLeft']" :filled="$item['filled']">
            {{ $item['title'] }}
        </x-ui.button>
    </a>
    @endforeach
</div>

@endif
