<?php

$lines = file('app/Filament/Resources/PageResource.php');
$inBlock = false;
$braceDepth = 0;
$bracketDepth = 0;
$schemaLineIndex = -1;

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];

    // Detect start of get...Block method
    if (preg_match('/protected static function get\w+Block\(\)/', $line)) {
        $inBlock = true;
        $braceDepth = 0;
        $schemaLineIndex = -1;
    }

    if ($inBlock) {
        if (strpos($line, '{') !== false) {
            $braceDepth += substr_count($line, '{');
        }
        if (strpos($line, '}') !== false) {
            $braceDepth -= substr_count($line, '}');
        }

        if ($schemaLineIndex === -1 && preg_match('/->schema\(\[/', $line)) {
            // Replace `->schema([` with `->schema(self::getBlockTabs([`
            $lines[$i] = preg_replace('/->schema\(\[/', '->schema(self::getBlockTabs([', $line, 1);
            $schemaLineIndex = $i;
            $bracketDepth = 0;
        }

        if ($schemaLineIndex !== -1) {
            if (strpos($line, '[') !== false) {
                $bracketDepth += substr_count($line, '[');
            }
            if (strpos($line, ']') !== false) {
                $bracketDepth -= substr_count($line, ']');
            }

            // If we closed the main array
            if ($bracketDepth === 0) {
                // Find where the array was closed, it should be something like `]);`
                if (preg_match('/\]\);/', $lines[$i])) {
                    $lines[$i] = preg_replace('/\]\);/', ']));', $lines[$i]);
                    $schemaLineIndex = -1; // Reset for next block
                }
            }
        }

        if ($braceDepth === 0 && strpos($line, '}') !== false) {
            $inBlock = false;
        }
    }
}

file_put_contents('app/Filament/Resources/PageResource.php', implode("", $lines));
echo "Done.\n";
