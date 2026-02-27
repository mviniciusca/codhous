@props(['icon' => null, 'type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer'
]) }}>
    {{ $slot }}
</button>
