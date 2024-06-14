@aware(['page'])
@props(['status' => null])

@if($status)
@livewire('newsletter', [
'title' => $status
])
@endif
