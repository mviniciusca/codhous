@props(['icon' => 'chevron-forward-outline', 'iconLeft' => null, 'filled' => null])

<button
    class="inline-flex items-center active:opacity-50 rounded-lg px-3 py-2.5 text-center text-sm font-medium focus:ring-4 {{ $filled ? 'bg-secondary-600 border border-secondary-900 hover:bg-secondary-500 text-primary-50 dark:focus:ring-secondary-900 focus:ring-secondary-400' : 'border border-primary-300 dark:border-primary-700 dark:hover:bg-primary-800 dark:hover:border-primary-950 hover:border-primary-200 focus:ring-primary-500 bg-opacity-10 bg-primary-300 dark:text-primary-50 dark:bg-opacity-5 dark:bg-primary-50'}}">

    @if($icon && $iconLeft)
    <x-ionicon :$icon />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if($icon && !$iconLeft)
    <x-ionicon :$icon />
    @endif

</button>
