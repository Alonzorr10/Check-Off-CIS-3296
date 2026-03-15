@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-lg text-stone-700 dark:text-stone-300']) }}>
    {{ $value ?? $slot }}
</label>
