<nav class="nav">
    <div class="nav-links">
        <ul class="flex space-x-6 justify-center">
        @auth
            {{-- Links for logged-in users --}}
            <a href="/events" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Events</a>
            <a href="/contributions-logged-in" class="nav-link">Contributions</a>
            
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="nav-link">Logout</button>
            </form>
        @endauth

        @guest
            {{-- Links for visitors --}}
            <a href="/home" class="nav-link">Home</a>
            <a href="/contributions" class="nav-link">Contributions</a>
            <a href="{{ route('login') }}" class="nav-link">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
        @endguest
    </div>
</ul>
</nav>