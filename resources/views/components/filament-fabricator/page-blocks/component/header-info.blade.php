@aware(['page'])
@props(['title', 'color', 'subtitle', 'status', 'position', 'title_font_size', 'section_filled', 'padding'])

@if ($status)
    <x-layout.section :$section_filled>
        <div
            class="{{ $position === 'center' ? 'text-center' : 'text-left' }} {{ $title_font_size === 'large'
                ? 'text-5xl md:text-5xl lg:text-6xl'
                : ($title_font_size === 'small'
                    ? 'text-xl md:text-2xl lg:text-4xl'
                    : 'text-xl md:text-2xl lg:text-4xl') }} mx-auto max-w-5xl">

            <span class="{{ $color === 'dark' ? 'text-primary-800' : ($color === 'light' ? 'text-primary-50' : '') }}">

                <h1
                    class="{{ $padding ? 'pt-8' : '' }} {{ $title_font_size === 'small' ? 'leading-normal tracking-tighter' : 'leading-none tracking-tighter' }} mb-4 font-bold">
                    {!! $title !!}
                </h1>

                <p class="mx-auto mb-8 max-w-4xl text-xl leading-tight tracking-tighter">
                    {!! $subtitle !!}
                </p>

            </span>

        </div>
    </x-layout.section>
@endif
