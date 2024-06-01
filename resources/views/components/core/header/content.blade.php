@foreach ($buttons['nav_button'] as $button )
<a href="{{ $button['button_link'] }}">
    <button
        class="mx-1 s mt-4 inline-flex items-center rounded px-3 py-2 text-sm focus:outline-none md:mt-0 active:opacity-80
    {{ $button['button_style'] === 'filled' ? 'bg-secondary-500 text-primary-50 hover:bg-secondary-700 border-none font-bold' : 'bg-none border text-primary-800 border-primary-700 hover:border-primary-700' }}">
        {!! $button['button_title'] !!}
        <x-ionicon :icon="$button['button_icon']" />
    </button>
</a>
@endforeach
