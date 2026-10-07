<?php
$blocks = [
    'partners', 'services', 'timeline', 'showcase', 'equipment_showcase',
    'faq', 'testimonials', 'coverage', 'cta', 'differentials', 'page_header',
    'stats', 'image_with_text', 'data_table', 'cards', 'featured_testimonial',
    'map', 'rich_text', 'contact_banner', 'payment_offer', 'module_reference'
];

@mkdir('resources/views/filament/block-previews', 0777, true);

foreach ($blocks as $block) {
    $content = <<<BLADE
<div class="pointer-events-none w-full bg-white">
    <x-render-block type="$block" :data="get_defined_vars()" />
</div>
BLADE;
    file_put_contents("resources/views/filament/block-previews/{$block}.blade.php", $content);
}
echo "Generated " . count($blocks) . " preview views.\n";
