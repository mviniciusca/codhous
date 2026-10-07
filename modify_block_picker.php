<?php

$content = file_get_contents('resources/views/vendor/filament-forms/components/builder/block-picker.blade.php');

$search = <<<'HTML'
    <div class="flex flex-col md:flex-row gap-6 mt-4">
        <!-- Sidebar -->
        <div class="w-full md:w-1/4 flex flex-col gap-2 border-r border-gray-200 dark:border-white/10 pr-4">
            <div class="font-semibold text-gray-900 dark:text-white mb-2">Categorias</div>
            @foreach($groupedBlocks as $cat => $catBlocks)
                <button class="flex items-center justify-between px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 transition-colors text-left text-gray-700 dark:text-gray-300">
                    <span>{{ $cat }}</span>
                    <span class="bg-gray-100 dark:bg-white/10 text-xs px-2 py-0.5 rounded-full">{{ count($catBlocks) }}</span>
                </button>
            @endforeach
        </div>

        <!-- Block Grid -->
        <div class="w-full md:w-3/4 max-h-[60vh] overflow-y-auto pr-2">
            @foreach($groupedBlocks as $cat => $catBlocks)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $cat }}</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
HTML;

$replace = <<<'HTML'
    <div style="display: flex; gap: 1.5rem; margin-top: 1rem; width: 100%;">
        <!-- Sidebar -->
        <div style="width: 25%; flex-shrink: 0; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 1rem; border-right: 1px solid rgba(156, 163, 175, 0.2);">
            <div style="font-weight: 600; margin-bottom: 0.5rem;" class="text-gray-900 dark:text-white">Categorias</div>
            @foreach($groupedBlocks as $cat => $catBlocks)
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 0.5rem; text-align: left;" class="text-sm hover:bg-gray-100 dark:hover:bg-white/5 transition-colors text-gray-700 dark:text-gray-300">
                    <span>{{ $cat }}</span>
                    <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;" class="bg-gray-100 dark:bg-white/10">{{ count($catBlocks) }}</span>
                </button>
            @endforeach
        </div>

        <!-- Block Grid -->
        <div style="width: 75%; max-height: 60vh; overflow-y: auto; padding-right: 0.5rem;">
            @foreach($groupedBlocks as $cat => $catBlocks)
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700;" class="text-gray-900 dark:text-white">{{ $cat }}</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
HTML;

$content = str_replace($search, $replace, $content);

// Now fix the card background color inside the grid loop
$cardSearch = <<<'HTML'
                            <div 
                                x-on:click="close; $wire.{{ $wireClickAction }}"
                                class="group cursor-pointer flex flex-col bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden hover:border-primary-500 dark:hover:border-primary-500 hover:ring-1 hover:ring-primary-500 transition-all duration-200"
                            >
                                <!-- Thumbnail Placeholder -->
                                <div class="h-28 bg-gray-100 dark:bg-black/20 w-full flex items-center justify-center relative p-4">
HTML;

$cardReplace = <<<'HTML'
                            <div 
                                x-on:click="close; $wire.{{ $wireClickAction }}"
                                style="display: flex; flex-direction: column; overflow: hidden; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.2); cursor: pointer; transition: all 0.2s;"
                                class="group bg-gray-50 dark:bg-gray-800/50 hover:border-primary-500 dark:hover:border-primary-500 hover:ring-1 hover:ring-primary-500"
                            >
                                <!-- Thumbnail Placeholder -->
                                <div style="height: 7rem; width: 100%; display: flex; align-items: center; justify-content: center; position: relative; padding: 1rem;" class="bg-gray-100 dark:bg-gray-900/50">
HTML;

$content = str_replace($cardSearch, $cardReplace, $content);

file_put_contents('resources/views/vendor/filament-forms/components/builder/block-picker.blade.php', $content);
echo "Modal Layout fixed.\n";

