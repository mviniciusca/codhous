@aware(['page'])
@props(['title', 'color', 'subtitle', 'status', 'position', 'title_font_size', 'section_filled', 'padding'])

@if($status)

<x-layout.section :$section_filled>
    <div class="mx-auto {{ $position === 'center' ? 'text-center' : 'text-left'}}
        {{ $title_font_size === 'large' ? 'text-4xl md:text-5xl lg:text-6xl' :
        ($title_font_size === 'small' ? 'text-xl md:text-2xl lg:text-3xl' :
         'text-xl md:text-2xl lg:text-4xl') }}">

        <span class="{{ $color === 'dark' ? 'text-primary-800' : ($color === 'light' ? 'text-primary-50' : '' ) }}">
            <h1 class="mb-4 {{ $padding ? 'pt-8' : '' }} font-bold
            {{ $title_font_size === 'large' ? 'leading-none tracking-tighter' : 'leading-normal tracking-normal' }}">
                {!! $title !!}</h1>
            <p class="mb-8 text-lg font-normal lg:text-xl">
                {!! $subtitle !!}</p>
        </span>

    </div>
</x-layout.section>

@endif
