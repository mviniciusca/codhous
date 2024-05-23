@aware(['page'])
@props(['images', 'only_images', 'only_title', 'only_subtitle', 'only_info', 'col_size', 'link'])

@if(count($images) != null)
<div
    class="grid grid-cols-2 gap-4 {{ ($col_size == 2 ? 'md:grid-cols-2' : ($col_size == 3 ? 'md:grid-cols-3' : ($col_size == 4 ? 'md:grid-cols-4' : ($col_size == 5 ? 'md:grid-cols-5' : 'md:grid-cols-6' )))) }}">

    @foreach ($images as $item )
    <a href="{{ $item['link'] }}">
        <div>
            <img class="h-auto max-w-full rounded-lg hover:opacity-80" src="{{ asset('storage/' . $item['image']) }}"
                alt="{{ $image['title'] ?? 'image' }}">
            @if(!$only_images)

            @if($only_subtitle)
            <p class="my-2 text-xs font-bold uppercase text-secondary-500">{{ $item['subtitle'] }}</p>
            @endif

            @if($only_title)
            <p class="text-xl font-bold leading-tight tracking-tight">{{ $item['title'] }}</p>
            @endif

            @if($only_info)
            <p class="my-1">{{ $item['info'] }}</p>
            @endif

            @endif
        </div>
    </a>
    @endforeach
</div>
@endif

@auth
{{-- Empty Section --}}
@if(count($images) == null)
<x-section.empty-section />
@endif
@endauth
