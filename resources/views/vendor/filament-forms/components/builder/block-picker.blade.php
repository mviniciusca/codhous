@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => '3xl',
])

<x-filament::dropdown
    :width="$width"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
    shift
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <div class="p-3">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 0.5rem; max-height: 60vh; overflow-y: auto;" class="pr-1">
            @foreach ($blocks as $block)
                @php
                    $wireClickActionArguments = ['block' => $block->getName()];

                    if (filled($afterItem)) {
                        $wireClickActionArguments['afterItem'] = $afterItem;
                    }

                    $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                    $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                @endphp

                <div 
                    x-on:click="close; $wire.{{ $wireClickAction }}"
                    class="flex flex-col items-center justify-center text-center p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 cursor-pointer hover:border-primary-500 hover:bg-primary-500 hover:shadow-md transition-all duration-200 group"
                >
                    <x-filament::icon
                        :icon="$block->getIcon()"
                        class="w-6 h-6 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-white transition-colors"
                    />
                    <span class="text-xs font-semibold text-gray-900 dark:text-gray-200 leading-tight group-hover:text-white transition-colors">
                        {{ $block->getLabel() }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::dropdown>
