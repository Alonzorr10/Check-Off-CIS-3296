<button {{ $attributes->merge(['type' => 'submit', 'class' => 'hover:ring-red-400 hover:ring-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-yellow-950 focus:ring-offset-2 dark:focus:ring-offset-stone-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
