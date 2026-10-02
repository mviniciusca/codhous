import os
import re

render_block = 'resources/views/components/render-block.blade.php'
with open(render_block, 'r') as f:
    content = f.read()

# Replace :bg-color="..." with :bg-color="..."\n            :text-color="$data['text_color'] ?? 'light'"
content = re.sub(r'(:bg-color="[^"]*")', r"\1\n            :text-color=\"$data['text_color'] ?? 'light'\"", content)

# For hardcoded sections in render-block:
# match `<section class="{{ $data['background_color'] ?? 'bg-background' }}`
# add `{{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}`
content = re.sub(
    r'(<section[^>]*class="\{\{ \$data\[\'background_color\'\] [^\}]+\}\})',
    r"\1 {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}",
    content
)

with open(render_block, 'w') as f:
    f.write(content)


def patch_component(filepath):
    with open(filepath, 'r') as f:
        comp_content = f.read()

    # Add textColor to props
    if '@props' in comp_content:
        # Check if textColor already in props to prevent duplicate
        if "'textColor'" not in comp_content:
            comp_content = re.sub(r"@props\(\[", r"@props([\n    'textColor' => 'light',", comp_content, count=1)
    
    # Replace `<section class="{{ $bgColor ?? 'bg-white' }}`
    # add `{{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}`
    
    m = re.search(r'<section[^>]*class="([^"]*?\{\{ \$bgColor[^\}]+\}\}[^"]*)"', comp_content)
    if m:
        full_class = m.group(1)
        if "text-scheme-dark" not in full_class:
            new_class = full_class.replace(
                "{{ $bgColor",
                "{{ $bgColor ?? 'bg-white' }} {{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} {{ $bgColor"
            )
            # Wait, replacing "{{ $bgColor" will duplicate it. Let's do it cleanly:
            new_class = full_class + " {{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}"
            comp_content = comp_content.replace(f'class="{full_class}"', f'class="{new_class}"')

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
