@props([
    'icon' => '
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
])

<span class="flex flex-wrap items-center justify-start gap-1">
    <span>
        {!! $icon !!}
    </span>

    @if (!$status->maintenance_mode)
        <span style="background-color: rgb(11, 124, 11)" class="animate-pulse rounded-md px-3 py-1 text-xs text-white">
            {{ __('Live') }}
        </span>
    @else
        <span style="background-color: rgb(223, 67, 67)"
            class="animate-pulse rounded-md px-3 py-1 text-xs tracking-wide text-white">
            {{ __('Off') }}
        </span>
        @if ($status->discovery_mode)
            <span class="animate-pulse rounded-md bg-primary-500 px-3 py-1 text-xs text-white">
                {{ __('Discovery') }}
            </span>
        @endif
    @endif

</span>
