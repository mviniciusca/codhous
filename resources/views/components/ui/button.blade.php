@props(['icon' => 'chevron-forward-outline', 'icon_left' => null, 'filled' => false])

<button
    class="inline-flex items-center active:opacity-50 rounded-lg px-5 py-2.5 text-center text-sm font-medium focus:ring-4 {{ $filled ? 'bg-secondary-600 hover:bg-secondary-500 text-primary-50 dark:focus:ring-secondary-900 focus:ring-secondary-400' : 'border border-primary-500 dark:border-primary-700 dark:hover:bg-primary-800 dark:hover:border-primary-950 focus:ring-primary-500'}}">
    <span class="{{ $icon_left ? 'order-1' : 'order-3' }}">
        {{ $slot }}
    </span>

    @if($icon)
    <x-ionicon class="order-2" :$icon />
    @endif

</button>
