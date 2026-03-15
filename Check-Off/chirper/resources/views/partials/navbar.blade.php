<nav class="nav bg-white border-b border-stone-200 px-6 py-4 flex items-center justify-between">
    <div class="nav-links font-bold text-lg tracking-tight">
        <ul class="flex font-medium gap-4 items-center text-stone-600 text-lg">
        @auth
            {{-- Links for logged-in users --}}
            <a href="/events" class="rounded-2xl hover:ring-2 hover:ring-yellow-500 transition-all px-6 py-3 nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Events</a>
            <a href="/contributions-logged-in" class="nav-link rounded-2xl hover:ring-2 hover:ring-yellow-500 transition-all px-6 py-3">Contributions</a>

            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="nav-link bg-yellow-500 text-white font-bold px-6 py-3 rounded-2xl border-yellow-600 hover:bg-stone-800 transition-all">Logout</button>
            </form>
            <a href='/profile' class='nav-link inline-flex items-center justify-center w-9 h-9 rounded-full hover:ring-1 hover:ring-stone-800 transition-all'>
                {{strtoupper(substr(Auth::user()->name,0,1))}}</a>
        @endauth

        @guest
            {{-- Links for visitors --}}
            <a href="/home" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-300 hover:border-stone-300 dark:hover:ring-stone-700 focus:outline-none focus:text-stone-700 dark:focus:text-stone-300 focus:border-stone-300 dark:focus:border-stone-700 transition duration-150 ease-in-out">Home</a>
            <a href="/contributions" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-300 hover:border-stone-300 dark:hover:ring-stone-700 focus:outline-none focus:text-stone-700 dark:focus:text-stone-300 focus:border-stone-300 dark:focus:border-stone-700 transition duration-150 ease-in-out">Contributions</a>
            <a href="{{ route('login') }}" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-300 hover:border-stone-300 dark:hover:ring-stone-700 focus:outline-none focus:text-stone-700 dark:focus:text-stone-300 focus:border-stone-300 dark:focus:border-stone-700 transition duration-150 ease-in-out">Login</a>
            <a href="{{ route('register') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-300 hover:border-stone-300 dark:hover:ring-stone-700 focus:outline-none focus:text-stone-700 dark:focus:text-stone-300 focus:border-stone-300 dark:focus:border-stone-700 transition duration-150 ease-in-out">Sign Up</a>
        @endguest
        </ul>
    </div>
</nav>