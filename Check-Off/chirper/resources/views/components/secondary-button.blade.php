<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-stone-200 dark:bg-stone-800 border border-stone-300 dark:border-stone-500 rounded-md font-semibold  text-stone-700 dark:text-stone-300 uppercase tracking-widest hover:bg-stone-50 dark:hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-yellow-700 focus:ring-offset-2 dark:focus:ring-offset-stone-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
