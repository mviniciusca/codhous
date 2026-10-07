<?php

$content = file_get_contents('resources/views/vendor/filament-forms/components/builder.blade.php');

$search = <<<'HTML'
                        <h4 class="text-xs font-bold tracking-wider text-gray-400 uppercase mb-3">{{ $cat }}</h4>
HTML;
$replace = <<<'HTML'
                        <h4 class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-300 uppercase mb-3">{{ $cat }}</h4>
HTML;
$content = str_replace($search, $replace, $content);

$search2 = <<<'HTML'
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $block->getLabel() }}
                                    </span>
HTML;
$replace2 = <<<'HTML'
                                    <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $block->getLabel() }}
                                    </span>
HTML;
$content = str_replace($search2, $replace2, $content);

$search3 = <<<'HTML'
                                    <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-500 group-hover:text-primary-500 group-hover:bg-primary-50 dark:group-hover:bg-primary-500/10 transition-colors">
HTML;
$replace3 = <<<'HTML'
                                    <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:text-primary-500 group-hover:bg-primary-50 dark:group-hover:bg-primary-500/10 transition-colors">
HTML;
$content = str_replace($search3, $replace3, $content);

file_put_contents('resources/views/vendor/filament-forms/components/builder.blade.php', $content);
echo "Sidebar colors fixed.\n";
