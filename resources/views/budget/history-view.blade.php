<div class="">
    @foreach ($data as $budget)

    <div class="mx-auto p-2 space-y-2">
        <div
            class="relative fi-fo-tabs flex flex-col fi-contained rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <h2
                        class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $budget->user->name }}
                    </h2>
                    <span class="fi-fo-field-wrp-helper-text break-words text-sm text-gray-500">
                        {{ $budget->updated_at }}
                    </span>
                </div>
                <p
                    class="mt-2 fi-section-header-description overflow-hidden break-words text-sm text-gray-500 dark:text-gray-400">
                    {{ $budget->user->name }} has {{ $budget->action === 'update' ? 'update' : 'create'
                    }} {{ ' this budget on ' . $budget->created_at }}</p>
            </div>
        </div>


    </div>



    @endforeach
</div>