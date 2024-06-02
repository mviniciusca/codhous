@props([
'status' => $content['status'],
'bg_repeat' => $content['bg_repeat'],
'bg_position' => $content['bg_position'],
'bg_attachment' => $content['bg_attachment'],
'bg_size' => $content['bg_size'],
'bg_height' => $content['bg_height']
])

{{ $bg_repeat }}
<div class="absolute -z-10 h-[680px] w-full bg-auto bg-scroll bg-center bg-no-repeat"
    style="background-image: url('{{asset('storage/' . $background)}}')">
</div>
