<?php

$lines = file('resources/views/components/render-block.blade.php');
$modified = false;

for ($i = 0; $i < count($lines); $i++) {
    // We only want to target <section class="..."> or <div class="..."> that represents the main block wrapper
    // Since each block might be slightly different, let's look for `<section class="`
    if (preg_match('/^\s*<section\s+class="([^"]+)"\s*(id="[^"]*")?\s*>/', $lines[$i], $matches)) {
        // If it doesn't already have custom_css_classes
        if (strpos($lines[$i], "custom_css_classes") === false) {
            $classContent = $matches[1];
            // Add {{ $data['custom_css_classes'] ?? '' }} to class
            $newClassContent = $classContent . " {{ \$data['custom_css_classes'] ?? '' }}";
            
            // Rebuild the line
            $newLine = preg_replace('/class="[^"]+"/', 'class="' . $newClassContent . '"', $lines[$i]);
            
            // Now add ID if not exists
            if (strpos($newLine, ' id=') === false) {
                // Insert ID before class
                $newLine = preg_replace('/<section\s+class="/', '<section id="{{ $data[\'custom_id\'] ?? \'\' }}" class="', $newLine);
            }
            
            $lines[$i] = $newLine;
            $modified = true;
        }
    }
}

if ($modified) {
    file_put_contents('resources/views/components/render-block.blade.php', implode("", $lines));
    echo "Modified render-block.blade.php\n";
} else {
    echo "No modifications needed.\n";
}

