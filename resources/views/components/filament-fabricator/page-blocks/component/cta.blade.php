@aware(['page'])
@props(['title', 'image', 'subtitle', 'btn_text', 'btn_url', 'status', 'axis', 'target'])

@if($status)
<section>
    <div class="mx-auto max-w-screen-xl items-center gap-8 px-4 py-8 sm:py-16 md:grid md:grid-cols-2 lg:px-6 xl:gap-16">
        <img class="order-{{ $axis ? '1' : '3' }} w-full rounded-md" src="{{ asset('storage/' . $image) }}" alt="image">
        <div class="order-2 mt-4 md:mt-0">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight">
                {!! $title !!}
            </h2>
            <p class="text-gray-500 mb-6 font-light md:text-lg">{!! $subtitle !!}</p>
            @if($btn_text)
            @if($btn_url )
            <a target="{{ $target ? '_blank' : '_self' }}" href="{{ $btn_url }}" @endif
                class="inline-flex items-center rounded-lg bg-secondary-600 px-5 py-2.5 text-center text-sm font-medium text-primary-50 hover:bg-secondary-500 focus:ring-4 focus:ring-secondary-400 dark:focus:ring-secondary-900">
                {{ $btn_text }}
                <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                @if($btn_url)
            </a>
            @endif
            @endif
        </div>
    </div>
</section>
@endif
