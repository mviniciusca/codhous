@props([
'status' => $content['status'],
'bg_repeat' => $content['bg_repeat'],
'bg_position' => $content['bg_position'],
'bg_attachment' => $content['bg_attachment'],
'bg_size' => $content['bg_size'],
'bg_height' => $content['bg_height']
])

@if($status)
<div class="absolute -z-10 w-full overflow-hidden" style="height:{{ $bg_height . 'px' }};">
    <img class="object-fit object-center" src="{{ asset('storage/' . $background) }}" />
</div>
@endif
