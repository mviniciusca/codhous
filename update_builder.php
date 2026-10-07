<?php
$file = 'app/Filament/Resources/PageResource.php';
$content = file_get_contents($file);

// Add ->blockPreviews() to the builder
$content = str_replace(
    "->blockNumbers(false),",
    "->blockNumbers(false)\n                                ->blockPreviews(),",
    $content
);

// Add ->preview() to each block
$content = preg_replace(
    "/(Forms\\\\Components\\\\Builder\\\\Block::make\('([^']+)'\))/m",
    "$1\n            ->preview('filament.block-previews.$2')",
    $content
);

file_put_contents($file, $content);
echo "Updated PageResource.php\n";
