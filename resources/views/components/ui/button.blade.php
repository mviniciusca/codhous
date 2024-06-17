@props([
'type' => 'submit',
'icon' => 'chevron-forward-outline',
'iconLeft' => null,
'filled' => null])

<button type="{{ $type }}"
    class="inline-flex items-center justify-center mx-auto active:opacity-80 rounded-md px-4 p-2 text-center text-sm font-medium focus:ring-4 hover:bg-primary-50 hover:bg-opacity-90 {{ $filled ? 'bg-secondary-500 border border-secondary-600 text-primary-50 dark:focus:ring-secondary-900 dark:hover:bg-secondary-900 bg-opacity-100 hover:bg-opacity-95 hover:bg-secondary-600 focus:ring-secondary-600' : 'border border-primary-300 dark:border-primary-700 dark:hover:bg-primary-800 dark:hover:border-primary-950 hover:border-primary-200 focus:ring-primary-500 bg-opacity-10 bg-primary-300 dark:text-primary-50 dark:bg-opacity-5 dark:bg-primary-50'}}">

    @if($icon && $iconLeft && $icon !== 'none')
    <x-ionicon :$icon />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if($icon && !$iconLeft && $icon !== 'none')
    <x-ionicon :$icon />
    @endif

</button>
