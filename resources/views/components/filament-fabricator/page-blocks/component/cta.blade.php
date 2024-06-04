@aware(['page'])
@props(['title', 'image', 'subtitle', 'btn_text', 'btn_url',
'status', 'axis', 'target', 'icon', 'iconLeft', 'filled'])

@if($status)
<section>
    <div class="mx-auto max-w-screen-xl items-center gap-8 px-4 py-8 sm:py-16 md:grid md:grid-cols-2 lg:px-6 xl:gap-16">
        <img class="order-{{ $axis ? '1' : '3' }} w-full rounded-md" src="{{ asset('storage/' . $image) }}" alt="image">
        <div class="order-2 mt-4 md:mt-0">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight">
                {!! $title !!}
            </h2>
            <p class="text-gray-500 mb-6 font-light md:text-lg">{!! $subtitle !!}</p>
            <x-ui.button :$icon :$iconLeft :$filled>{{ $btn_text }}</x-ui.button>
        </div>
    </div>
</section>
@endif
