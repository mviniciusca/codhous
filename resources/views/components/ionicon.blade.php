@props(['icon' => null, 'size' => null])

<ion-icon
    class="hover:opacity-60 cursor-pointer active:opacity-40 transition-all duration-100 px-1 {{ $size == 'big' ? 'text-3xl' : 'text-xl' }}"
    name="{{ $icon }}">
</ion-icon>