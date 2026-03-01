<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['page'])
<x-filament-fabricator.layouts.default :page="$page" >

{{ $slot ?? "" }}
</x-filament-fabricator.layouts.default>