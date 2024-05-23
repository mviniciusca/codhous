@aware(['page'])
@props(['images', 'only_images'])

<div class="grid grid-cols-2 gap-4 md:grid-cols-3">
    @foreach ($images as $item )
    <div>
        <img class="h-auto max-w-full rounded-lg" src="{{ asset('storage/' . $item['image']) }}" alt="">
        <p>{{ $item['title'] }}</p>
        <p>{{ $item['subtitle'] }}</p>
    </div>
    @endforeach
</div>
