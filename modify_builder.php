<?php

$content = file_get_contents('vendor/filament/forms/resources/views/components/builder.blade.php');

$sidebarHtml = <<<'HTML'
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div style="display: flex; gap: 1.5rem; width: 100%; flex-wrap: nowrap; align-items: flex-start;">
        <!-- Sidebar do Builder -->
        <div style="width: 280px; flex-shrink: 0; position: sticky; top: 1.5rem; max-height: calc(100vh - 3rem); overflow-y: auto;" class="space-y-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl p-4 hidden lg:block shadow-sm">
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Adicionar Bloco</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Clique para adicionar no fim da página.</p>
            
            <div class="grid grid-cols-1 gap-2">
                @foreach($blockPickerBlocks as $block)
                    @php
                        $wireClickActionArguments = \Illuminate\Support\Js::from(['block' => $block->getName()]);
                        $wireClickAction = "mountFormComponentAction('{$statePath}', '{$addAction->getName()}', {$wireClickActionArguments})";
                    @endphp
                    <div 
                        x-on:click="$wire.{{ $wireClickAction }}"
                        class="group cursor-pointer flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-white/5 rounded-lg border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all"
                    >
                        <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:text-primary-500 group-hover:bg-primary-50 dark:group-hover:bg-primary-500/10 transition-colors">
                            <x-filament::icon
                                :icon="$block->getIcon()"
                                class="h-4 w-4"
                            />
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ $block->getLabel() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Área Principal -->
        <div class="flex-1 min-w-0">
            <div
                x-data="{}"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->class(['fi-fo-builder grid grid-cols-1 gap-y-4'])
                }}
            >
HTML;

$content = str_replace('<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([\'fi-fo-builder grid grid-cols-1 gap-y-4\'])
        }}
    >', $sidebarHtml, $content);

$content = str_replace('</x-dynamic-component>', "        </div>\n    </div>\n</x-dynamic-component>", $content);

file_put_contents('resources/views/vendor/filament-forms/components/builder.blade.php', $content);
echo "Sidebar restored in builder.blade.php without categories.\n";

