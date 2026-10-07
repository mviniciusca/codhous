<?php
$blocks = [
    'partners', 'services', 'timeline', 'showcase', 'equipment_showcase',
    'faq', 'testimonials', 'coverage', 'cta', 'differentials', 'page_header',
    'stats', 'image_with_text', 'data_table', 'cards', 'featured_testimonial',
    'map', 'rich_text', 'contact_banner', 'payment_offer', 'module_reference'
];

foreach ($blocks as $block) {
    $content = <<<BLADE
@php
    \$id = 'preview_' . uniqid();
    \$html = view('filament.block-previews.iframe-wrapper', [
        'type' => '$block', 
        'data' => get_defined_vars(), 
        'id' => \$id
    ])->render();
@endphp
<div class="w-full relative">
    <!-- Overlay invisível para evitar qualquer interação com o iframe -->
    <div class="absolute inset-0 z-50 cursor-grab active:cursor-grabbing"></div>
    <iframe id="{{ \$id }}" class="w-full border-0 rounded-2xl shadow-sm bg-white" scrolling="no" srcdoc="{{ \$html }}" style="min-height: 100px;"></iframe>
</div>

<script>
    if (!window.hasPreviewListener) {
        window.addEventListener('message', function(event) {
            if (event.data.type === 'resize' && event.data.id) {
                const iframe = document.getElementById(event.data.id);
                if (iframe) {
                    iframe.style.height = event.data.height + 'px';
                }
            }
        });
        window.hasPreviewListener = true;
    }
</script>
BLADE;
    file_put_contents("resources/views/filament/block-previews/{$block}.blade.php", $content);
}
echo "Fixed double escaping.\n";
