<div class="p-4 space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Event</p>
            <p class="text-base font-semibold">{{ ucfirst($activity->event ?? 'N/A') }}</p>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date/Time</p>
            <p class="text-base">{{ $activity->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Log Name</p>
            <p class="text-base">{{ $activity->log_name ?? 'N/A' }}</p>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject Type</p>
            <p class="text-base">{{ class_basename($activity->subject_type ?? 'N/A') }}</p>
        </div>
    </div>

    @if($activity->description)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
        <p class="text-base">{{ $activity->description }}</p>
    </div>
    @endif

    @if($activity->properties && $activity->properties->isNotEmpty())
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Properties</p>
        <div class="bg-gray-100 dark:bg-gray-800 rounded p-3">
            <pre class="text-xs overflow-auto">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    @endif
</div>
