@aware(['page'])
@props(['title', 'subtitle', 'status', 'position', 'title_font_size'])

@if($status)
<div class="px-4 py-4">
    <div class="mx-auto {{ $position === 'center' ? 'text-center' : 'text-left'}}
        {{ $title_font_size === 'large' ? 'text-4xl md:text-5xl lg:text-6xl' :
        ($title_font_size === 'small' ? 'text-xl md:text-2xl lg:text-3xl' :
         'text-xl md:text-2xl lg:text-4xl') }}">
        <h1 class="mb-4 font-bold {{ $title_font_size === 'large' ? 'leading-tight tracking-tighter' :
            'leading-none tracking-tight' }}">
            {!! $title !!}</h1>
        <p class="mb-8 text-lg font-normal lg:text-xl">
            {!! $subtitle !!}</p>
    </div>
</div>
@endif
