@props([
    'icon' => '
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
])

<div class="flex flex-wrap items-center justify-start gap-1 text-xs tracking-normal text-white">
    <span>
        {!! $icon !!}
    </span>

    @if (!$status->maintenance_mode)
        <span class="animate-pulse rounded-md bg-green-600 px-3 py-1">
            {{ __('Live') }}
        </span>
    @else
        <span class="animate-pulse rounded-md bg-red-600 px-3 py-1">
            {{ __('Off') }}
        </span>
        @if ($status->discovery_mode)
            <span class="animate-pulse rounded-md bg-indigo-500 px-3 py-1">
                {{ __('Discovery') }}
            </span>
        @endif
    @endif

</div>
