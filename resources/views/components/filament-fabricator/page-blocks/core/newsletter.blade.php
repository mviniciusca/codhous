@aware(['page'])
@props(['status' => null, 'title' => null, 'subtitle' => null, 'info' => null, 'btn_text' => null])

@if($status)
@livewire('newsletter', [
'title' => $title,
'subtitle' => $subtitle,
'info' => $info,
'btn_text' => $btn_text,
])
@endif
