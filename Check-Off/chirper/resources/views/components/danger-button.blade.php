<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full flex items-center justify-center px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold uppercase tracking-widest rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-stone-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
