<div>
    @foreach ($content->social as $item )
    <div class="inline-flex p-1 hover:opacity-70 cursor-pointer">
        <a target="new" href="{{ $item['link'] }}">
            <x-ionicon :icon="$item['icon']" />
        </a>
    </div>
    @endforeach
</div>