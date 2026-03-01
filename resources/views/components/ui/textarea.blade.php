@props(['disabled' => false, 'error' => false])

@php
$baseClasses = 'w-full rounded-md border px-4 py-3 focus:outline-none focus:ring-1 transition-colors disabled:cursor-not-allowed disabled:opacity-50 resize-y min-h-[80px]';
$colorClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500 text-red-900 placeholder:text-red-300' 
    : 'border-border bg-background text-foreground focus:border-primary focus:ring-primary placeholder:text-muted-foreground';
@endphp

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $baseClasses . ' ' . $colorClasses]) !!}>{{ $slot }}</textarea>
