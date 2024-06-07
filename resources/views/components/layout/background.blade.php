@props(['content'])

@if($content)
@if($content['status'])
<div class="absolute -z-10 w-full overflow-hidden" style="height:{{ $content['bg_hight'] . 'px' }};">
    <div class="h-full w-full {{ $content['bg_size'] . ' ' . $content['bg_repeat'] . ' ' . $content['bg_position'] . ' ' . $content['bg_attachment'] }}"
        style="background-image: url('{{ asset('storage/' . ($background ? $background : '')) }}')">
    </div>
</div>
@endif
@endif
