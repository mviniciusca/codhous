@props(['section_filled' => null ])

<section class="py-8 {{ $section_filled ? 'bg-primary-950 bg-opacity-5 dark:bg-opacity-25' : '' }}">
    {{ $slot }}
</section>
