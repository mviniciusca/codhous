import os
import re

render_block = 'resources/views/components/render-block.blade.php'
with open(render_block, 'r') as f:
    content = f.read()

# Replace <x-section-partners ... > with <x-section-partners :bg-color="$data['background_color'] ?? null" ... >
# We can do a regex replacement.
content = re.sub(r'(<(?:x-section-|livewire:section-)[a-zA-Z0-9_-]+)', r'\1\n            :bg-color="$data[\'background_color\'] ?? null"', content)

# For hardcoded sections in render-block:
content = re.sub(r'<section class="bg-background', r'<section class="{{ $data[\'background_color\'] ?? \'bg-background\' }}', content)
content = re.sub(r'<section id="orcamento" class="bg-muted/50', r'<section id="orcamento" class="{{ $data[\'background_color\'] ?? \'bg-muted/50\' }}', content)

with open(render_block, 'w') as f:
    f.write(content)


def patch_component(filepath):
    with open(filepath, 'r') as f:
        comp_content = f.read()

    # Find the <section class="..."> and replace bg-*
    # match `<section class="bg-white py-16"` -> `<section class="{{ $bgColor ?? 'bg-white' }} py-16"`
    
    # Extract the original bg color class
    m = re.search(r'<section[^>]*class="([^"]*?bg-(?:white|background|muted/50|slate-50)[^"]*)"', comp_content)
    if not m:
        return
        
    full_class = m.group(1)
    
    # We want to replace the `bg-*` part
    # Actually, simpler: replace the exact bg class found
    bg_match = re.search(r'(bg-(?:white|background|muted/50|slate-50))', full_class)
    if bg_match:
        orig_bg = bg_match.group(1)
        new_class = full_class.replace(orig_bg, f"{{{{ $bgColor ?? '{orig_bg}' }}}}")
        comp_content = comp_content.replace(f'class="{full_class}"', f'class="{new_class}"')

    # Now add bgColor to props
    if '@props' in comp_content:
        # insert into existing @props
        comp_content = re.sub(r'@props\(\[', r"@props([\n    'bgColor' => null,", comp_content, count=1)
    else:
        # add @props at the top
        comp_content = "@props(['bgColor' => null])\n" + comp_content

    with open(filepath, 'w') as f:
        f.write(comp_content)

for root, _, files in os.walk('resources/views/components'):
    for file in files:
        if file.startswith('section-') and file.endswith('.blade.php'):
            patch_component(os.path.join(root, file))

for root, _, files in os.walk('resources/views/livewire'):
    for file in files:
        if file.startswith('section-') and file.endswith('.blade.php'):
            patch_component(os.path.join(root, file))

print("Patch complete.")
