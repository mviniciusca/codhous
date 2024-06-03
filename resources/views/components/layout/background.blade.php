@props([
'status' => $content['status'],
'bg_repeat' => $content['bg_repeat'],
'bg_position' => $content['bg_position'],
'bg_attachment' => $content['bg_attachment'],
'bg_size' => $content['bg_size'],
'bg_height' => $content['bg_height']
])

@if($status)
<div id="app-backrgound" class="absolute -z-10 w-full
{{ $bg_repeat . ' '. $bg_position . ' '. $bg_attachment . ' ' . $bg_size . ' ' }}"
    style="background-image: url('{{asset('storage/' . $background)}}'); height:{{ $bg_height }}px">
</div>
@endif
