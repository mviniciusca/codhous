@props(['icon' => null, 'size' => null])

<ion-icon class="px-1 {{ $size == 'big' ? 'text-3xl' : 'text-xl' }}" name="{{ $icon }}"></ion-icon>