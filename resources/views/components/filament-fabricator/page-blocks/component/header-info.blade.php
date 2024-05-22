@aware(['page'])
@props(['title', 'subtitle', 'status', 'position'])

@if($status)
<div class="px-4 py-4 md:py-8">
    <div class="mx-auto max-w-7xl {{ $position === 'center' ? 'text-center' : 'text-left'}}">
        <h1 class="dark:text-white mb-4 text-4xl font-extrabold leading-tight tracking-tighter md:text-5xl lg:text-6xl">
            {!! $title !!}</h1>
        <p class="dark:text-gray-400 mb-8 text-lg font-normal lg:text-xl">
            {!! $subtitle !!}</p>
    </div>
</div>
@endif
