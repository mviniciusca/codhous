@aware(['page'])
@props(['padding'])

<div
    class="w-full {{ $padding === 'small' ? 'py-4': ($padding === 'medium' ? 'py-8' : ($padding === 'large' ? 'py-12' : 'py-24')) }}">
</div>
