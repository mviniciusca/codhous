@aware(['page'])
@props(['images', 'only_images', 'only_title', 'only_subtitle', 'only_info'])

<div class="grid grid-cols-2 gap-4 md:grid-cols-3">
    @foreach ($images as $item )
    <div>
        <img class="h-auto max-w-full rounded-lg" src="{{ asset('storage/' . $item['image']) }}" alt="">
        @if(!$only_images)
        @if($only_title)
        <p>{{ $item['title'] }}</p>
        @endif
        @if($only_subtitle)
        <p>{{ $item['subtitle'] }}</p>
        @endif
        @if($only_info)
        <p>{{ $item['info'] }}</p>
        @endif
        @endif
    </div>
    @endforeach
</div>
