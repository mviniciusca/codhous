@aware(['page'])
@props(['content'])

@foreach ($content as $item)
@if ($item['status'])
<section>
    <div class="mx-auto max-w-screen-xl items-center gap-8 px-4 py-8 sm:py-16 md:grid md:grid-cols-2 lg:px-6 xl:gap-16">

        @if($item['image'])
        <img class="order-{{ $item['axis'] ? '1' : '3' }} w-full rounded-md"
            src="{{ asset('storage/' . $item['image']) }}" alt="image">
        @endif

        <div class="order-2 mt-4 md:mt-0">

            @if($item['title'])
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight">
                {!! $item['title']!!}
            </h2>
            @endif

            @if($item['subtitle'])
            <p class="mb-6 font-light text-gray-500 md:text-lg">{!! $item['subtitle'] !!}</p>
            @endif

            @if($item['btn_text'] || $item['icon'])
            <a target="{{ $item['target'] }}" href="{{ $item['btn_url'] ? $item['btn_url'] : '#' }}">
                <x-ui.button :icon="$item['icon']" :iconLeft="$item['iconLeft']" :filled="$item['filled']">
                    {{ $item['btn_text'] }}
                </x-ui.button>
            </a>
            @endif

        </div>
    </div>
</section>
@endif
@endforeach
