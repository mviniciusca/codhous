@props(['buttons'])

<div class="inline-flex items-center gap-2 py-2">
    @if($buttons['nav_button'])
    @foreach ($buttons['nav_button'] as $button)
    @if($button['status'])
    <a target="{{$button['target'] ? '_blank' : '_self'}}" href="{{ $button['link'] ? $button['link'] : '#' }}">
        <x-ui.button :filled="$button['filled']" :iconLeft="$button['iconLeft']" :icon="$button['icon']">
            {!! $button['title'] !!}
        </x-ui.button>
    </a>
    @endif
    @endforeach
    @endif
    {{-- Darkmode Switch --}}
    @livewire('darkmode')
</div>
