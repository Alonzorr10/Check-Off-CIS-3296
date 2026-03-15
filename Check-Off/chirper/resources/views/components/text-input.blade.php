@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 focus:border-yellow-700 dark:focus:border-yellow-600 focus:ring-yellow-700 dark:focus:ring-yellow-600 rounded-md']) }}>
