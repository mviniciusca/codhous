@props(['size' => null])

<div>
    @if($content->social !== null)

    @foreach ($content->social as $item )
    <div class="inline-flex p-1 cursor-pointer">
        <a target="new" href="{{ $item['link'] }}">
            <x-ionicon :size="$size" :icon="'logo-'.$item['icon']" />
        </a>
    </div>
    @endforeach

    @endif
</div>