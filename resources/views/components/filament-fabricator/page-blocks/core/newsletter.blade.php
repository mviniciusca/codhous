@aware(['page'])
@props(['status' => null, 'title' => null, 'subtitle' => null, 'info' => null, 'btn_text' => null])

@if($status)
@livewire('newsletter', [
'info' => $info,
'title' => $title,
'subtitle' => $subtitle,
'btn_text' => $btn_text,
])
@endif
