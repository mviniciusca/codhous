@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground']) }}>
    {{ $value ?? $slot }}
</label>
