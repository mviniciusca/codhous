@php($url = $getRecord()->thumbnailUrl())
<div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 flex items-center justify-center">
    @if ($url)
        <img src="{{ $url }}" alt="{{ $getRecord()->name }}" loading="lazy" class="max-h-full max-w-full object-contain">
    @else
        <x-filament::icon icon="heroicon-o-photo" class="h-10 w-10 text-gray-400" />
    @endif
</div>
