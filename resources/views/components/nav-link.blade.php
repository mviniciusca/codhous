@props(['active'])

@php
    $classes = $active ?? false ? 'flex gap-4 p-4 items-center w-full text-ls font-bold bg-white rounded-full' : 'flex gap-4 p-4 items-center w-full text-ls';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
