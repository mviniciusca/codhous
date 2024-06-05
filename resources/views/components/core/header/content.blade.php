@props(['buttons' => null])

@if($buttons)
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
